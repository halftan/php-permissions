<?php
/**
 * Created by PhpStorm.
 * User: halftan
 * Date: 15/七月/15
 * Time: 18:09
 */

namespace Permissions;


/**
 *
 * Interface of matcher.
 * All matchers should implement this interface.
 *
 * @author Andy Zhang
 */
interface IMatcher
{
    /**
     * @return array
     */
    public function match();
}