### 0.9.1alpha (2011-04-22)
* Added addDir() method to phFileViewLoader so it can search in different folders
* Altered setValidator methods so they return the validator that was passed in and can be chained
* Added a numeric validator (phNumericValidator) that can validate whole and decimal numbers optionally requiring a minimum and maximum
* Added '.' capability to phFormView's id method so you can refer to subforms elements
* Added Nathans email validator with unit tests and modifications to make it only validate public emails and to make it more RFC 5321 & 5322 compliant
* BUGFIX Subforms of subforms where not having their names set properly
