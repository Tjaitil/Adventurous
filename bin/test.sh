export $(grep -v '^#' .env | xargs)

/Applications/MAMP/bin/php/php8.2.0/bin/php test.php CityCentreTest upgrade  