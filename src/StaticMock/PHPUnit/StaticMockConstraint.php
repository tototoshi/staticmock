<?php
namespace StaticMock\PHPUnit;

use StaticMock\Exception\AssertionFailedException;


class StaticMockConstraint extends \PHPUnit_Framework_Constraint
{

    private $assertion_error_message;

    /**
     * Evaluates the constraint for parameter $other. Returns TRUE if the
     * constraint is met, FALSE otherwise.
     *
     * @param mixed $other Value or object to evaluate.
     * @return bool
     */
    protected function matches($other)
    {
        try {
            $other->assert();
            return true;
        } catch (AssertionFailedException $e) {
            $this->assertion_error_message = $e->getMessage();
            return false;
        }
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString()
    {
        if ($this->assertion_error_message === null) {
            return "mock object doesn't satisfy constraint";
        } else {
            return lcfirst($this->assertion_error_message);
        }
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * To provide additional failure information additionalFailureDescription
     * can be used.
     *
     * @param  mixed $other Evaluated value or object.
     * @return string
     */
    protected function failureDescription($other)
    {
        return $this->toString();
    }

}
