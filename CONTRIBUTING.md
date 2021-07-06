
# Contributing code

To submit pull requests, please first [fork the repository](https://help.github.com/articles/fork-a-repo/).

On your fork, create a new branch and add your changes to that branch.

Submit a [pull request](https://help.github.com/articles/creating-a-pull-request/) from your updated branch to the Theme Check repository.

In your pull request's description, please explain your update and reference the associated issue you're fixing.
Include testing instructions.

After you send your proposed changes, one of the maintainers will test and review the pull request. After it's reviewed and the changes are accepted by at least one of the maintainers, someone will merge the pull request.

## Testing your code

You can use Composer to install PHP CodeSniffer and test your code against the WordPress coding standards.

https://getcomposer.org/

### Commands

Install the dependencies:
`composer install`

Check the code:
`composer standards:check`

Automated fixes:
`composer standards:fix`

# Contributing to issues

GitHub issues are how we track changes to the plugin that need to be discussed and completed.
Ideally, all changes are broken down into issues that can be addressed by discussion or a pull request.

Before creating an issue, please search the existing issues to see if it has been discussed before. If not, create an issue and the maintainers will add labels as appropriate.

Issues should be descriptive but not too long. Use text, screenshots, and screen recordings to communicate the issue.

# Reviewing pull requests

All code changes happen through a pull request made by contributors, ideally associated with an issue.

If you're not already using Git, you may benefit from installing the [GitHub desktop application.](https://desktop.github.com)

This will allow you to download the repository in one click, keep it in sync, and easily switch between different pull requests.

Once a pull request is selected in the application, create a zip file of the whole repository, and upload it to your site to test.

Otherwise, you can test a pull request by pulling down the associated branch, creating a zip file of the contents, and uploading to your WordPress site via the admin. This repository includes all files, so it will install just like any other uploaded plugin.

Once you've tested and reviewed, please report your findings by adding a review or comment to the pull request on GitHub.
