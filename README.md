A PHP website for calculating a user's GPA based on their grades.

Implements a login system to store user's grades so that their information can persist over multiple sessions. 

Special care was made to seperate concerns by having the HTML structure written entirely in it's own document. The PHP
code loads it in and modifies it using PHP's DOM implementation.

The focus of the application was on the functionality of the application and the appearance of the product.

Security was not part of the requirements, and therefore not a priority in the implementation. User data is
stored serverside as a serialized PHP object.

Further development should focus on security to enhance user trust in the application.
