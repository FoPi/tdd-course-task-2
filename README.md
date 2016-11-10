Write a PHP application in a TDD manner that will get the first popular question from stackoverflow and returns with user_id of the answers. Use mocking in order to be independent from the API.
Use the following API calls:
PHP Mocking: https://phpunit.de/manual/current/en/test-doubles.html
https://api.stackexchange.com/docs/featured-questions (get the first)
https://api.stackexchange.com/docs/answers-on-questions (get the user_ids)