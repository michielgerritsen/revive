# Revive

Got a Magento 2 store where the integration test always fails with some database errors? You can do an attempt to fix them manually by [following this blogpost](https://www.michiel-gerritsen.com/debugging-the-magento-2-integration-test-setup/), or use this tool. This tried to find the root cause of why your tests are failing and apply a fix for them.

## What's in the name?

We've been all in that place: A shiny new project. You can do everything right this time! But as times goes by and deadlines needs to get meet, testing may not be very high on you priority list. When you finally want to start writing tests it turns out that your test setup is broken.

That's why it's called Revive: It helps you to revive this feeling at the start of the project: You ARE going to do this better this. Heck, you've already got this far that you are trying to run integration tests.

## Usage

You have 2 options to use Revive:

- Download `revive.phar` from the [release tab](https://github.com/michielgerritsen/revive/releases).
- Clone this repository and run `composer install`. You can then use revive by using `php src/revive.php --root-dir=/path/to/your/magento/installation`. 

## Testing

You can run the tests using PHPUnit:

`vendor/bin/phpunit`
