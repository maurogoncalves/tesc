<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\Usuario;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required', 'message'=>'O campo e-mail é obrigatório'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\Usuario',
                'filter' => ['status' => Usuario::STATUS_ATIVO],
                'message' => 'Não encontramos nenhum usuário com este e-mail.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = Usuario::findOne([
            'status' => Usuario::STATUS_ATIVO,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }
        
        if (!Usuario::isPasswordResetTokenValid($user->passwordResetToken)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Restauração da Senha do sistema '.Yii::$app->name)
            ->send();
    }
}
