<?php
/**
 * @category   Core
 * @package    Core_Tests
 * @subpackage UnitTests
 * @group      Core_Tests
 * @group      Core_Tests_PHPUnit
 */
class Core_Tests_PHPUnit_Constraint_DomQueryTest
    extends PHPUnit_Framework_TestCase
{
    public function testShouldAllowMatchingOfAttributeValues()
    {
        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title>ZF Issue ZF-4010</title>
    </head>
    <body>
        <form>
            <fieldset id="fieldset-input"><legend>Inputs</legend>
                <ol>
                    <li><input type="text" name="input[0]" id="input-0" value="value1" /></li>
                    <li><input type="text" name="input[1]" id="input-1" value="value2" /></li>
                    <li><input type="text" name="input[2]" id="input-2" value="" /></li>
                </ol>
            </fieldset>
        </form>
    </body>
</html>';
        $assertion = new Core_Tests_PHPUnit_Constraint_DomQuery(
            'input#input-0 @value',
            Core_Tests_PHPUnit_Constraint_DomQuery::ASSERT_CONTENT_CONTAINS,
            'value1'
        );
        $result = $assertion->evaluate($html);
        $this->assertTrue($result);
    }
}
