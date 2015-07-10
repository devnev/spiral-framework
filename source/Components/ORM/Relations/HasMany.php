<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 * @copyright ©2009-2015
 */
namespace Spiral\Components\ORM\Relations;

use Spiral\Components\ORM\ActiveRecord;

class HasMany extends HasOne
{
    const RELATION_TYPE = ActiveRecord::HAS_MANY;

    const MULTIPLE = true;
}