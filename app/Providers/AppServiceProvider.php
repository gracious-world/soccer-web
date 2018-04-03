<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use Illuminate\Hashing\BcryptHasher;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 自定义密码规则, 只允许字母和数字，并且不允许连续3位相同字符
        Validator::extend('custom_admin_password', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(?=.*\d+)(?=.*[a-zA-Z]+)(?!.*?([a-zA-Z0-9]{1})\1\1).{8,16}$/', $value);
        });
        // 首字母为英文字母或数字
        Validator::extend('custom_first_character', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[a-zA-Z][a-zA-Z0-9]+$/', $value);
        });
        // 验证hash前字符串是否一致
        Validator::extend('different_before_hash', function ($attribute, $value, $parameters, $validator) {
            $hash = new BcryptHasher;
            $this->requireParameterCount(1, $parameters, 'different_before_hash');
            $other = $parameters[0];
            $bChecked = ( isset($this->data[$other]) && (! $hash->check($value, $this->data[$other])) );
            return $bChecked;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
