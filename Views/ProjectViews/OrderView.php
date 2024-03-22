<?php
declare(strict_types=1);

namespace Views\ProjectViews;

use Views\AbstractViews\AbstractOrderView;
use Models\ProjectModels\Session\User\SessionModel;

class OrderView extends AbstractOrderView
{
    public function __construct()
    {
        parent::__construct(SessionModel::getInstance());
    }

    /**
     * @param string $valueTitle
     * @return string|null
     * @throws \Exception
     */
    public function getFormValue(string $valueTitle): ?string
    {
        $valueTitle = strtolower($valueTitle);
        $this->validateFormValueTitle($valueTitle);
        $value = null;
        if ($this->userSessModel->isLoggedIn()) {
            $valueData = $this->userSessModel->getCustomerData()[$valueTitle] ?? null;
            $valueData = !is_null($valueData) && $valueTitle === 'phone' ? '+' . $valueData : $valueData;
            $value = is_null($valueData) && $valueTitle === 'phone' ? "value='+380'" : "value='$valueData'";
        } else {
            $value = $valueTitle === 'phone' ? "value='+380'" : null;
        }

        return $value;
    }

    /**
     * @param string $valueTitle
     * @throws \Exception
     */
    private function validateFormValueTitle(string $valueTitle): void
    {
        switch (strtolower($valueTitle)) {
            case 'name' :
            case 'email' :
            case 'phone' :
            case 'address' :
                break;
            default:
                throw new \Exception(
                    'Unknown $valueTitle : ' . "'$valueTitle', during trying to select logged in user data!"
                );
        }
    }

}
