<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels;

use Models\AbstractProjectModels\Message\AbstractBaseMsgModel;
use Models\ProjectModels\Session\Filter\SessionModel;

abstract class AbstractFilterModel
{
    protected SessionModel $filterSessionModel;
    protected ?AbstractBaseMsgModel $msgModel = null;

    public function __construct()
    {
        $this->filterSessionModel = SessionModel::getInstance();
    }

    /**
     * @param array $data
     * @param string $controllerName
     * @param string $actionName
     * @throws \Exception
     */
    public function setFilter(array $data, string $controllerName, string $actionName): void
    {
        $filterData = $this->getFormatFilterDataMethod($data, $controllerName, $actionName);
        $this->filterSessionModel->setFilter($controllerName, $actionName, $filterData);
    }

    /**
     * @param array $data
     * @param string $controllerName
     * @param string $actionName
     * @return array|null
     * @throws \Exception
     */
    protected function getFormatFilterDataMethod(array $data, string $controllerName, string $actionName): ?array
    {
        switch ($controllerName) {
            case 'catalog' :
                return $this->getFormatCatalogFilterDataMethod($data, $actionName);
            default :
                throw new \Exception(
                    'Unknown controller name : ' . "'$controllerName'" .
                    ', during trying to select formatFilterDataMethod!'
                );
        }
    }

    /**
     * @param array $data
     * @param string $actionName
     * @return array|null
     * @throws \Exception
     */
    protected function getFormatCatalogFilterDataMethod(array $data, string $actionName): ?array
    {
        switch ($actionName) {
            case 'show' :
                return $this->formatCatalogShowFilterDataMethod($data);
            default :
                throw new \Exception(
                    'Unknown action name : ' . "'$actionName'" .
                    ', during trying to select formatFilterDataMethod!'
                );
        }
    }

    protected function formatCatalogShowFilterDataMethod(array $data): array
    {
        $result = [];

        if (isset($data['author_id'])) {
            $result['condition_data']['author_id'] = $data['author_id'];
        }

        if (isset($data['genre_id'])) {
            $result['condition_data']['genre_id'] = $data['genre_id'];
        }

        if (isset($data['rating'])) {
            $result['condition_data']['rating'] = $data['rating'];
        }

        if (isset($data['price'])) {
            $result['condition_data']['price'] = $data['price'];
        }

        if (isset($data['quantity'])) {
            $result['condition_data']['quantity'] = $data['quantity'];
        }

        if (
            isset($result['condition_data']['rating']) ||
            isset($result['condition_data']['price']) ||
            isset($result['condition_data']['quantity'])
        ) {
            foreach ($result['condition_data'] as $field => $value) {
                if ($field === 'rating' || $field === 'price' || $field === 'quantity') {
                    $result['sql_operators'][] = $data[$field . '_operator'];
                } else {
                    $result['sql_operators'][] = '=';
                }
            }
        } else {
            $result['sql_operators'] = null;
        }

        if (!isset($result['condition_data'])) {
            $result['condition_data'] = null;
        } else {
            foreach ($result['condition_data'] as $conditionData) {
                $result['and_or_operators'][] = 'AND';
            }

            array_shift($result['and_or_operators']);
            $result['and_or_operators'] = empty($result['and_or_operators']) ? null : $result['and_or_operators'];
        }

        if (isset($data['order_by'])) {
            $result['order_by'] = $data['order_by'];
            $result['order_by_type'] = $data['order_by_type'] ?? null;
        }

        return $result;
    }

    /**
     * @param string $controllerName
     * @param string $actionName
     * @throws \Exception
     */
    public function clearFilter(string $controllerName, string $actionName): void
    {
        $this->filterSessionModel->unsetFilter($controllerName, $actionName);
        $this->msgModel->setMessage('success', 'success_clear_filter', 'success_clear_filter');
    }

    public function issetFilter(string $controllerName, string $actionName): bool
    {
        return $this->filterSessionModel->issetFilter($controllerName, $actionName);
    }

    public function getFilter(string $controllerName, string $actionName): array
    {
        return $this->filterSessionModel->getFilter($controllerName, $actionName);
    }

    public function setMessageModel(AbstractBaseMsgModel $msgModel): void
    {
        $this->msgModel = $msgModel;
    }
}
