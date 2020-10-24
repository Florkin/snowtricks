<?php

namespace App\Form\Model;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ChangePassword
{
    /**
     * @SecurityAssert\UserPassword(
     *     message = "Wrong value for your current password"
     * )
     */
    protected $oldPassword;

    protected $newPassword;

    protected $newPasswordConfirm;

    public function oldPasswordIsValid(UserPasswordEncoderInterface $passwordEncoder)
    {
        return $passwordEncoder->isPasswordValid($this->oldPassword);
    }

    public function passwordConfirmIsValid()
    {
        return $this->newPassword === $this->newPasswordConfirm;
    }

    /**
     * @return mixed
     */
    public function getNewPasswordConfirm()
    {
        return $this->newPasswordConfirm;
    }

    /**
     * @return mixed
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @return mixed
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @param mixed $newPasswordConfirm
     */
    public function setNewPasswordConfirm($newPasswordConfirm): void
    {
        $this->newPasswordConfirm = $newPasswordConfirm;
    }

    /**
     * @param mixed $oldPassword
     */
    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @param mixed $newPassword
     */
    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    /**
     * @Assert\IsTrue(message="The passwords are not the sames")
     */
    public function isPasswordConfirmed()
    {
        return $this->newPassword == $this->newPasswordConfirm;
    }

}