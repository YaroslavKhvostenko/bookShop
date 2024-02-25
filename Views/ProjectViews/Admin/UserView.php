<?php
declare(strict_types=1);

namespace Views\ProjectViews\Admin;

use Views\AbstractViews\Admin\AbstractUserView;

class UserView extends AbstractUserView
{
    private const REQUIRED_PROFILE_ITEMS = [
        'phone' => 'phone',
        'address' => 'address'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->profileItems['required'] = array_merge(
            $this->profileItems['required'], self::REQUIRED_PROFILE_ITEMS
        );
    }
}
