# linkvalidator
Development Fork of TYPO3s sysext LinkValidator

This readme is used as scratchpad for this development version.

Will be removed for final core version

## Description of current functionality (before rewrite)

### Link checking

Parameters:
* TSconfig: searchFields (where to search for links: table.field)
* checkhidden
* perms (use permissions of current user to access records)
* startPage : start searching beginning with current page
* depth : depth of scan
* linktypes : which type of links to check (external, internal, ...)

Checking links

1. Get list of pids for current critea (startpage, depth etc.) by 
   using current pid to recursively retreive pids
2. For every pid the page TSconfig must be considered
3. Delete all current records in tx_linkvalidator_link for list
   of pids
4. Get list of records for pids by using configured searchFields:
   Iterate through table.fields and retrieve all records for 
   list of pids
5. Retrieve links from records
6. Check links by using check function from LinkType class, 
   , consider hooks, write results into tx_linkvalidator_link     


There is also an option 'showalllinks' which will show
all links, not just broken links (not really used currently).

Current flaws:
* having the TSconfig anywhere in the page tree and / or
  user / group TSconfig doesn't even work - currently
  searchField is only used from startpage
  
Possible chnages:
* simplify configuration possibilities if they don't really
  make sense
* make configuration unnecessary as far as possible - provide
  sane defaults
 

## Changes:

Rewrite:
- Use Fluid instead of marker template
- Database access in Repository
- 

General changes
- There is only 1 configuration per site (for "Check Links"),
  not page / user / group TSconfig
- Some things that were set for "Link Check" will now only
  be selectable for "Link Report" (if at all) : perms, showhidden
- The link check is always done the same: entire site
- The link check should be done via scheduler : show appropriate
  hints 
- provide wizards which already suggest sensible defaults 
  (e.g. for scheduler)  

GUI
- Module now under Site Management (not in Info module)
- Icon for Module
- Reduce width of some columns, e.g. merge anchor and url into 
  1 column, truncate some strings

List of broken links:
- Add icons for view page, refresh

## Incoming ideas


