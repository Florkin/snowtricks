<?php

namespace App\Handlers\Forms;

use Symfony\Component\Form\Forms;

abstract class AbstractFormHandler
{
    public function getFormFactory() {
        return Forms::createFormFactoryBuilder();
    }
}