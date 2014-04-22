<?php

/** 
 *  @Embeddable
 *  @Label("name")
 */
class FoobarParent
{
    /** @String */
    public $name;

    /** @String */
    public $xxx;
}

/** @Persist */
class foobar
{
    /** @Id */
    public $id;

    /** @Email @Required @List */
    public $email;

    /** @String @Required */
    public $first_name; 

    /** @String */
    public $last_name; 

    /** @String @Longtext */
    public $foobar; 

    /** @Int */
    public $age; 

    /** @Embed(foobarparent) */
    public $rel; 
}
