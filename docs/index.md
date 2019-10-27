# One File CMS
The newer rewrite of the good'ol OneFileCMS built for PHP 7+ with a modern approach in design.

> Note: You can access the legacy version in the `legacy` branch [HERE](gh-legacy).

## Why a rewrite?
While the original OneFileCMS did the job just right, it was not updated for a while. And with depreciation of PHP 5 for
the newer PHP 7, it was not updated to reflect those changes. Additionally, when it came to maintenance it was difficult
to pin-point where the issue was (at least in my case, since I have couple projects depending on this CMS). I wanted to
do a rewrite so the that OneFileCMS can be relevant in my projects as well as provide an updated version to those
interested on what this project is about and be able to use it.

### This goal of this project is to:
+ Keep the legacy as is, but in the `legacy` branch of this repo.
+ Modular - look for ways to ensure a separation of components but the end product MUST be a single file (hence 
  OneFileCMS).
+ Modern Design - with the boom of various JS frameworks and CSS3, we can make a UI easier than it was couple years
  back, as well as, keep utilize a SPA app approach.
+ Security - with PHP 7 comes new ways to ensure we have a secure system, try and integrate that into the project.

## Contribution
As of now, I'm just evaluating how I can perform a rewrite while not making it too complicated in the future. So
development is still in it's infancy. I will update the soon when I would be willing to accept pull requests, once I've
determined what route to take.

You're still invited to propose an approach for the rewrite, just create a issue with the `discussion` tag and we can
discuss more about it there.

[gh-legacy]: https://github.com/creativenobu/OneFileCMS/tree/legacy
