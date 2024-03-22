<?php
declare(strict_types=1);

namespace Models\ProjectModels;

use Models\AbstractProjectModels\AbstractBookModel;
use Models\ProjectModels\Session\User\SessionModel;

class BookModel extends AbstractBookModel
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }

    public function getBookDetails(int $bookId): ?array
    {

        $result = $this->getBookData($bookId);
        $result['book_comments'] = $this->getBookComments($bookId);
        if (!is_null($result['book_rating']) && $this->sessionModel->isLoggedIn()) {
            $rating = $this->getLoggedInUserBookRating($bookId);
            if (!is_null($rating)) {
                $result['logged_user_set_book_rating'] = $rating;
            }
        }

        return $result;
    }

    private function getBookData(int $bookId): ?array
    {
        $requestFields = [
            'books_catalog`.`image' => 'book_image',
            'books_catalog`.`id' => 'book_id',
            'books_catalog`.`title' => 'book_title',
            'books_catalog`.`rating' => 'book_rating',
            'books_catalog`.`pub_date' => 'book_pub_date',
            'books_catalog`.`price' => 'book_price',
            'books_catalog`.`description' => 'book_description',
            'authors`.`name' => 'book_author',
            'genres`.`title' => 'book_genre'
        ];
        $joinTables = [
            'genres',
            'authors'
        ];
        $joinConditions = [
            'books_catalog`.`genre_id' => 'genres`.`id',
            'books_catalog`.`author_id' => 'authors`.`id'
        ];
        $joinTypes = [
            'JOIN',
            'JOIN'
        ];
        $conditionData['books_catalog`.`id'] =  $bookId;
        $dbResult = $this->db->select($requestFields)->from(
                ['books_catalog'],
                $joinTables,
                $joinConditions,
                $joinTypes
            )->condition($conditionData)->
            query()->fetch();
        if (!$dbResult) {
            throw new \Exception(
                'Din\'t find book in `books_catalog` using `books_catalog`.`id` :' . $bookId . ' !'
            );
        }

        return $dbResult;
    }

    private function getBookComments(int $bookId): ?array
    {
        $conditionData['book_id'] = $bookId;
        $requestFields = [
            'id',
            'user_id',
            'author_name',
            'comment',
            'date'
        ];
        $dbResult = $this->db->select($requestFields)->
            from(['books_comments'])->
            condition($conditionData)->
            query()->fetchAll();
        if (!$dbResult) {
            return null;
        }

        return $dbResult;
    }

    private function getLoggedInUserBookRating(int $bookId): ?int
    {
        $userId = $this->sessionModel->getCustomerData()['id'];
        if (is_null($userId)) {
            throw new \Exception(
                'Didn\'t find logged_in user id data in session data!'
            );
        } else {
            $userId = (int)$userId;
        }

        if (!$this->db->select(['id'])->from(['users'])->condition(['id' => $userId])->query()->fetch()) {
            throw new \Exception(
                'Didn\'t find logged_in user id data in DB, using id from session data!'
            );
        }

        $conditionData['book_id'] = $bookId;
        $conditionData['user_id'] = $userId;
        $dbResult = $this->db->select(['rating'])->
        from(['books_rating'])->
        condition($conditionData, ['AND'])->
        query()->fetch();
        if (!$dbResult) {
            return null;
        }

        return (int)$dbResult['rating'];
    }

    public function addItem(array $data, string $productOption, int $productId): void
    {
        switch ($productOption) {
            case 'rating' :
                $this->addRating($data, $productId);
                break;
            case 'comment' :
                $this->addComment($data, $productId);
                break;
            default :
                throw new \Exception('Unknown product option name : ' . "'$productOption' !");
        }
    }

    protected function addComment(array $data, int $productId): void
    {
        if ($this->sessionModel->isLoggedIn()) {
            $userId = $this->sessionModel->getCustomerData()['id'] ?? null;
            if (is_null($userId)) {
                throw new \Exception(
                    'Didn\'t find user id in session data'
                );
            } else {
                $data['user_id'] = (int)$userId;
            }
        }

        $data ['book_id'] = $productId;
        if ($this->db->insertData('books_comments', $data)) {
            $this->msgModel->setMessage('success', 'comment', 'success_comment');
        }
    }


    /**\
     * @param array $data
     * @param int $productId
     * @throws \Exception
     */
    protected function addRating(array $data, int $productId): void
    {
        if ($this->sessionModel->isLoggedIn()) {
            $result = $this->getLoggedInUserBookRating($productId);
            if (!is_null($result)) {
                throw new \Exception(
                    'Trying to add logged in user book rating, 
                    but it already exist for book in DB `books_catalog` with book `id` ' . $productId . ' !'
                );
            }
        }

        if ($this->sessionModel->isLoggedIn()) {
            $userId = $this->sessionModel->getCustomerData()['id'] ?? null;
            if (is_null($userId)) {
                throw new \Exception(
                    'Didn\'t find user id in session data'
                );
            } else {
                $data['user_id'] = (int)$userId;
            }
        }

        $data['book_id'] = $productId;
        try {
            $this->db->beginTransaction();
            $this->db->insertData('books_rating', $data);
            $this->updateBookRating($productId);
            $this->db->commit();
            $this->msgModel->setMessage('success', 'rating', 'success_add_rating');
        } catch (\PDOException $exception) {
            $this->db->rollBack();
            $this->msgModel->setErrorMsg();
        }
    }

    protected function updateBookRating(int $productId): void
    {
        $dbResult = $this->db->select(['rating'])->
        from(['books_rating'])->
        condition(['book_id' => $productId])->
        query()->fetchAll();
        $countRatingMarks = count($dbResult);
        $summaryRatingMarks = 0;
        foreach ($dbResult as $bookData) {
            $summaryRatingMarks += (int)$bookData['rating'];
        }
        $bookRating = $summaryRatingMarks / $countRatingMarks;
        if (is_float($bookRating)) {
            $bookRating = round($bookRating, 2);
        }

        $this->db->update(['books_catalog'], ['rating' => $bookRating])->condition(['id' => $productId])->exec();
    }

    public function updateItem(array $data, string $productOption, int $productId): void
    {
        switch ($productOption) {
            case 'rating' :
                $this->updateRatingMark($data, $productId);
                break;
            default :
                throw new \Exception('Unknown product option name : ' . "'$productOption' !");
        }
    }

    protected function updateRatingMark(array $data, int $productId): void
    {
        $ratingMark = $this->getLoggedInUserBookRating($productId);
        if (is_null($ratingMark)) {
            throw new \Exception(
                'Cannot update user rating mark because it doesnt exist in DB!'
            );
        }

        if ($ratingMark === (int)$data['rating']) {
            $this->msgModel->setMessage('failure', 'self_rating', 'failure_self_rating');

            return;
        }

        $userId = $this->sessionModel->getCustomerData()['id'] ?? null;
        if (is_null($userId)) {
            throw new \Exception('Didnt find user id in session data!');
        } else {
            $userId = (int)$userId;
        }

        try {
            $this->db->beginTransaction();

            $this->db->update(['books_rating'], $data)->condition(
                ['book_id' => $productId, 'user_id' => $userId],
                null,
                ['AND']
            )->exec();
            $this->updateBookRating($productId);
            $this->db->commit();
            $this->msgModel->setMessage('success', 'rating', 'success_update_rating');
        } catch (\PDOException $exception) {
            $this->db->rollBack();
            $this->msgModel->setErrorMsg();
        }
    }
}
