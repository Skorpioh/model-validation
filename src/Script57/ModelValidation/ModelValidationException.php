<?php namespace Script57\ModelValidation;

class ModelValidationException extends \RuntimeException {

	/**
	 * Validation error messages.
	 *
	 * @var mixed
	 */
	protected $errors;


	/**
	 * Set the validation error messages.
	 *
	 * @param  $errors
	 * @return ModelValidationException
	 */
	public function setErrors($errors)
	{
		$this->errors = $errors;
		$this->message = "The model validation failed.";

		return $this;
	}


    /**
     * Return the validation error messages.
     *
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

}
