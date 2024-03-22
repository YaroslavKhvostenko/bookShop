<?php
declare(strict_types=1);

namespace Models\AbstractProjectModels\Validation\Data\Filter\Set;

use http\Exception\InvalidArgumentException;
use Models\AbstractProjectModels\Validation\Data\Filter\AbstractBaseValidator as BaseValidator;

abstract class AbstractBaseValidator extends BaseValidator
{
    protected ?string $controller = null;
    protected ?string $action = null;

    /**
     * @param string $controllerName
     * @throws \Exception
     */
    public function setControllerName(string $controllerName): void
    {
        $this->validateControllerName($controllerName);
        $this->controller = $controllerName;
    }

    /**
     * @param string $actionName
     * @throws \Exception
     */
    public function setActionName(string $actionName): void
    {
        $this->validateActionName($actionName);
        $this->action = $actionName;
    }

    /**
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function emptyCheck(array $data): array
    {
        if (empty($data)) {
            throw new \Exception(
                'Array $data is empty!'
            );
        }

        $result = [];
        $arrayUnique = array_unique($data);
        if (count($arrayUnique) === 1 && $arrayUnique[array_key_first($arrayUnique)] === '') {
            $result['empty_filter_' . $this->controller . '_' . $this->action] = '';
        } else {
            foreach ($data as $field => $value) {
                if ($value !== '') {
                    $result[strtolower($field)] = !is_numeric($value) ? strtolower($value) : $value;
                }
            }
        }

        return $this->getEmptyCheckSqlOperatorsMethod($result);
    }

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    protected function getEmptyCheckSqlOperatorsMethod(array $data): ?array
    {
        switch ($this->controller) {
            case 'catalog':
                return $this->getCatalogEmptyCheckSqlOperatorsMethod($data);
            default:
                throw new \Exception(
                    'Unknown controller name : '. "'$this->controller'" .
                    ' to select emptyCheckSqlOperatorsMethod!'
                );
        }
    }

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    protected function getCatalogEmptyCheckSqlOperatorsMethod(array $data): ?array
    {
        switch ($this->action) {
            case 'show' :
                return $this->catalogShowEmptyCheckSqlOperatorsMethod($data);
            default:
                throw new \Exception(
                    'Unknown action name : '. "'$this->action'" . ' to select emptyCheckSqlOperatorsMethod!'
                );
        }
    }

    protected function catalogShowEmptyCheckSqlOperatorsMethod(array $data): ?array
    {
        if (isset($data['price']) && !isset($data['price_operator'])) {
            $data['empty_filter_catalog_show_price_operator'] = '';
        }

        if (isset($data['rating']) && !isset($data['rating_operator'])) {
            $data['empty_filter_catalog_show_rating_operator'] = '';
        }

        if (isset($data['quantity']) && !isset($data['quantity_operator'])) {
            $data['empty_filter_catalog_show_quantity_operator'] = '';
        }

        if (isset($data['price_operator']) && !isset($data['price'])) {
            $data['empty_filter_catalog_show_price'] = '';
        }

        if (isset($data['rating_operator']) && !isset($data['rating'])) {
            $data['empty_filter_catalog_show_rating'] = '';
        }

        if (isset($data['quantity_operator']) && !isset($data['quantity'])) {
            $data['empty_filter_catalog_show_quantity'] = '';
        }

        return $data;
    }

    /**
     * @param string $controllerName
     * @throws \Exception
     */
    protected function validateControllerName(string $controllerName): void
    {
        switch ($controllerName) {
            case 'catalog' :
                break;
            default :
                throw new \Exception(
                    'This controller :' . ucfirst($controllerName) . 'Controller ' .
                    'does not have filter!'
                );
        }
    }

    /**
     * @param string $actionName
     * @throws \Exception
     */
    protected function validateActionName(string $actionName): void
    {
        switch ($this->controller) {
            case 'catalog' :
                $this->validateCatalogActions($actionName);
                break;
            default :
                throw new \Exception(
                    'This controller :' . ucfirst($this->controller) . 'Controller ' .
                    'does not have filter!'
                );
        }
    }

    /**
     * @param string $actionName
     * @throws \Exception
     */
    protected function validateCatalogActions(string $actionName): void
    {
        switch ($actionName) {
            case 'show' :
                break;
            default :
                throw new \Exception(
                    'This controller :' . $actionName . 'Controller ' .
                    'does not have filter!'
                );
        }
    }

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    public function correctCheck(array $data): ?array
    {
        return $this->getCorrectCheckMethod($data);
    }

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    protected function getCorrectCheckMethod(array $data): ?array
    {
        switch ($this->controller) {
            case 'catalog':
                return $this->getCatalogCorrectCheckMethod($data);
            default:
                throw new \Exception(
                    'Unknown controller name : '. "'$this->controller'" . ' to select correctCheckMethod!'
                );
        }
    }

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    protected function getCatalogCorrectCheckMethod(array $data): ?array
    {
        switch ($this->action) {
            case 'show' :
                return $this->catalogShowCorrectCheckMethod($data);
            default:
                throw new \Exception(
                    'Unknown action name : '. "'$this->action'" . ' to select correctCheckMethod!'
                );
        }
    }

    /**
     * @param array $data
     * @return array|null
     * @throws \Exception
     */
    protected function catalogShowCorrectCheckMethod(array $data): ?array
    {
        $resultData = [];
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'author_id' :
                case 'genre_id' :
                    $resultData[$field] = $this->isNumericId($value);
                    break;
                case 'rating_operator':
                case 'price_operator':
                case 'quantity_operator':
                    $resultData[$field] = $this->isCorrectSqlMathOperator($value);
                    break;
                case 'price' :
                case 'rating' :
                    $result = $this->isNumeric($value);
                    if ($result === '') {
                        $resultData['wrong_filter_catalog_show_' . $field] = '';
                    } else {
                        $resultData[$field] = $this->isFloatStringData($value) ? (float)$value : (int)$value;
                    }

                    break;
                case 'quantity' :
                    $result = $this->isNumeric($value);
                    if ($result === '') {
                        $resultData['wrong_filter_catalog_show_' . $field] = '';
                    } else {
                        $resultData[$field] = (int)$value;
                    }
                    break;
                case 'order_by' :
                    $resultData[$field] = $this->checkOrderByFieldValue($value);
                    break;
                case 'order_by_type' :
                    $resultData[$field] = $this->checkOrderByTypeFieldValue($value);
                    break;
                default:
                    throw new InvalidArgumentException(
                        'Unknown field name : ' ."'$field'". ', from property $field in provided array data!!!'
                    );
            }
        }

        return $resultData;
    }

    /**
     * @param string $id
     * @return int|null
     * @throws \Exception
     */
    protected function isNumericId(string $id): ?int
    {
        if (!is_numeric($id)) {
            throw new \Exception(
                'id have to be numeric'
            );
        }

        return (int)$id;
    }

    /**
     * @param string $value
     * @return string|null
     * @throws \Exception
     */
    protected function isCorrectSqlMathOperator(string $value): ?string
    {
        switch ($value) {
            case '=' :
            case '>' :
            case '<' :
                return $value;
            default :
                throw new \Exception('Wrong operator for sql request! You received :' . "'$value' !");
        }
    }

    protected function isNumeric(string $value): string
    {
        return is_numeric($value) ? $value : '';
    }

    protected function isFloatStringData(string $value): bool
    {
        preg_match('/\./', $value , $matches);
        if (isset($matches[0]) && $matches[0] === '.') {
            return true;
        }

        return false;
    }

    /**
     * @param string $value
     * @return string|null
     * @throws \Exception
     */
    protected function checkOrderByFieldValue(string $value): ?string
    {
        switch (strtolower($value)) {
            case 'book_id' :
            case 'author_id' :
            case 'genre_id' :
            case 'rating' :
            case 'price' :
            case 'quantity' :
                return strtolower($value);
            default :
                throw new \Exception('Unknown value : ' . "'$value'" .  'for field \'order_by\'');
        }
    }

    /**
     * @param string $value
     * @return string
     * @throws \Exception
     */
    protected function checkOrderByTypeFieldValue(string $value): string
    {
        if (strtoupper($value) !== 'DESC') {
            throw new \Exception('Field \'order_by_type\' can have only \'DESC\' value!!!');
        }

        return strtoupper($value);
    }
}
