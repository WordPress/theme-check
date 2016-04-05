# Contributing to Theme Check

The following is a set of guidelines for contributing to Theme Check. I'll add to this as needed.

## When to create new checks?

You'd be surprised how often this comes up for new contributors.

Every check in Theme Check exists in a self-contained PHP file in the /checks directory. These should be separated by a means which lets us all keep them sane. This has not been done in the past, so the sanity level there is somewhat uneven. :)

* If you're contributing to a new issue which specifically references some new check to be added, then the resulting code should be in a new PHP file in the checks directory. 
* If you're modifying some existing check, then the resultsing code should modify the existing check file.
* If you're reorganizing, or otherwise obsoleting an existing check, then the resulting code should consolidate all the relevant check code into a new check file, and remove or rewrite the old one as appropriate.

## How to properly submit Pull Requests

A lot of new or even long time GitHub users get this surprisingly wrong. Follow these steps:

1. A pull request should encompass only a *single* idea. Not "general cleanup" or "a bunch of assorted changes I made to my fork of the repository". One pull request = one change. 
2. A pull request should come from a *branch* you made in your fork of the repository. Not from your master copy. If you want to work on a specific problem, then make a fork of the repo, and then branch that fork to encompass what you're going to be working on. Then, when you submit the pull-request, you can switch back to your master or another branch and work on something else without polluting the pull request with other unrelated changes.
3. Ideally, a pull request should reference a specific *issue* that already exists and which has has some discussion on the topic. If you just make changes that you want to see, and then find that we didn't need those changes or don't want them for some reason you didn't think about, then that's a waste of your valuable time. Talk out what the problems you see are before you start writing the code to fix them. In a project, people must coordinate and work together to accomplish the goals. Code-first-and-ask-questions-later doesn't really work out well for anybody.
4. If a pull request has a bunch of extra changes in it that are not related to the single problem, then it will probably get rejected/closed without additional discussion. This is not a judgment on the correctness of the idea, or on your code. But asking maintainers to cherry-pick through your code for the relevant parts is basically just asking them to rewrite your whole submission anyway. In which case, why did you bother with the pull request? Take your time to make a branch and get it right, so that your code can be accepted with minimal overhead and problems. You're not the only person working on the project. :)

