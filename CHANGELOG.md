### 0.9.3beta (2011-05-22)
* Added a 3rd argument to the phForm constructor that allows the ability to pass custom variable's to the view
* Added array access to phValidatorLogic so you can get access to the validators it contains
* Added a phForm::validator() helper method that returns the validator set on a dataitem in the form.  E.g. $form->validator()->name would give you the validator set on the name field.
* Added hasData($name) method to phFormView that returns true if the data identified by $name is present in the view
* BUGFIX - phNumericValidator was not returning true or false out of doValidate, which meant the validator didn't work properly.  Fixed & updated tests.

### 0.9.2alpha (2011-05-04)
* Changed all the validators so they pass empty values.  Apart from phRequiredValidator of course which now also validates arrays, failing them if they have zero elements.  
* Added a more graceful check in phFormView that makes sure a view templates content is UTF-8 before passing it through to SimpleXMLElement.  If it is not valid UTF-8 a more graceful exception is thrown.
* Changed phForm::__toString() so any exceptions are caught and trigger_error is called with a message that explains the exception thrown.  This stops PHP throwing another exception (phForm::__toString() must not throw an exception) and as a result masking the original problem.
* BUGFIX - When no value was bound to a simple array data item an error occurred - https://github.com/mellowplace/Minacl/issues/7 
* BUGFIX - issue https://github.com/mellowplace/PHP-HTML-Driven-Forms/issues/5 - unit tests hanging if you didn't have the xhtml dtd installed locally.  Have altered the validation to use the XHTML schema (.xsd) instead and have packaged these locally in test/resources/schema
* BUGFIX - https://github.com/mellowplace/PHP-HTML-Driven-Forms/issues/2 - empty textarea tags where rendered as < textarea /> which is bad HTML

### 0.9.1alpha (2011-04-22)
* Added addDir() method to phFileViewLoader so it can search in different folders
* Altered setValidator methods so they return the validator that was passed in and can be chained
* Added a numeric validator (phNumericValidator) that can validate whole and decimal numbers optionally requiring a minimum and maximum
* Added '.' capability to phFormView's id method so you can refer to subforms elements
* Added Nathans email validator with unit tests and modifications to make it only validate public emails and to make it more RFC 5321 & 5322 compliant
* BUGFIX Subforms of subforms where not having their names set properly