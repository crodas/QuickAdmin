<?php

/** @Persist */
class ReferenceTest {
    /** @Id */
    public $id;

    /** @String */
    public $name;
}

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

    /** @Reference(referencetest, Select => name) */
    public $xreference;

    /** @String @Longtext */
    public $foobar; 

    /** @Int */
    public $age; 

    /** @Embed(foobarparent) */
    public $rel; 
}
