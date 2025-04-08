<?php
declare(strict_types=1);

namespace Cake\ORM;

use Cake\Validation\Validator;

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         6.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
class TableValidator extends Validator
{
    /**
     * @inheritDoc
     */
    public function normalizeRuleArray(string $name, array $rule): array
    {
        $rule = parent::normalizeRuleArray($name, $rule);

        if (!is_array($rule['callable']) || !$rule['callable'][0] instanceof Table) {
            return $rule;
        }

        // For behavior methods which are proxied through the table class, we need
        // to fish out the behavior instance and use that as the callable, since
        // `ValidationRule` uses reflection decide whether the context needs to be passed
        // to the method, when processing the rule.
        $table = $rule['callable'][0];
        if (
            !method_exists($table, $rule['callable'][1])
            && $table->behaviors()->hasMethod($rule['callable'][1])
        ) {
            foreach ($table->behaviors() as $behavior) {
                if (in_array($rule['callable'][1], $behavior->implementedMethods(), true)) {
                    $rule['callable'][0] = $behavior;
                    break;
                }
            }
        }

        return $rule;
    }
}
