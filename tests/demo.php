<?php

/** @Persist */
class foobar
{
    /** @Email @Required */
    public $email;

    /** @String @Required */
    public $first_name; 

    /** @String */
    public $last_name; 

    /** @String @Longtext */
    public $foobar; 

    /** @Int */
    public $age; 
}
