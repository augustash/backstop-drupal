# August Ash Backstop JS VRT Tool

This tool will use a globally installed version of Backstop to run Visual Regression Testing on your machine using a local url and comparing to a live url.

----
To properly run this tool you will need to ensure you are on Node Version 16 or higher and first install Backstop JS globally.
`npm install -g backstopjs`

## Setup and Use
1. `cd` into your project root
2. create a new directory called "tests" - `mkdir tests`
3. `cd` into the new "tests" directory - `cd tests`
4. Run `backstop init` to initialze backstop on this project
5. Return to the root of your project.
6. Ensure this package is installed with a `composer require augustash/backstop-js`
7. Run `composer backstop-setup` from the root of your project and input your local and live urls when prompted
8. `cd` back to the tests directory - `cd tests`
9. Create a reference point for backstop to check against - `backstop reference`
10. Run a test on your site once changes are made - `backstop test`
11. Review and update anything as needed.

----
Note that it is a good idea to remove reference and test images before committing anything.

----
## Options
- When running the `composer backstop-setup` command you will be prompted for a few input items. <br/>
  - The two required options will be your local DDEV url to test with along with the live url to reference.<br/>
  - By default the test with run against nodes 1-5, but you will be prompted to pass any additional you may want to test.<br/>
  - By default the `.captcha` is hidden from the tests so it will be rendered with a `visibility: hidden` option, you can also pass additional items if needed.<br/>
  - By default the `.eu-cookie-compliance-banner` is removed from the tests and these will be removed from the DOM, you can also pass additional items to this as needed.