<?php 
declare(strict_types=1); 

namespace app\models; 

use Yii; 
use yii\base\Model; 

/** 
 * LoginForm is the model behind the login form. 
 * 
 * @property-read User|null $user 
 */
class LoginForm extends Model 
{ 
    public $username = ''; 
    public $password = ''; 
    public $rememberMe = true; 
    private $_user = null; 
    private $_userLoaded = false; 

    /** 
     * @return array the validation rules. 
     */ 
    public function rules() 
    { 
        return [ 
            // Username dan password wajib diisi
            [['username', 'password'], 'required'], 
            // rememberMe harus bernilai boolean
            ['rememberMe', 'boolean'], 
            // Password divalidasi menggunakan fungsi validatePassword()
            ['password', 'validatePassword'], 
        ]; 
    } 

    /** 
     * Validates the password. 
     */ 
    public function validatePassword(string $attribute, array|null $params = null): void 
    { 
        if (!$this->hasErrors()) { 
            $user = $this->getUser(); 
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        } 
    } 

    /** 
     * Logs in a user using the provided username and password. 
     */ 
    public function login(): bool 
    { 
        if ($this->validate()) { 
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0); 
        } 
        return false; 
    } 

    /** 
     * Finds user by [[username]] 
     */ 
    public function getUser() 
    { 
        if (!$this->_userLoaded) { 
            $this->_user = User::findByUsername($this->username); 
            $this->_userLoaded = true; 
        } 
        return $this->_user; 
    } 
}