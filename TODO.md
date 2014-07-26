TANGARA-UI TODO LIST
==========

Thematic list
-------------
- Project
  * auto detect locale 
    User
      * DONE Selector menu for ProjectOwnerGroup that contains groups of the user
      * DONE Create a group in the same time user is created
      * DONE user shouldn't have projects
      * DONE Check if user is granted to project
    Group
      * DONE User could have private group for managing private projects
      * DONE Edition
      * DONE Add upload
      * DONE Requested groups list on profile page
      * DONE Group manager mail confirmation sending
      * DONE Group template: list of members
      * DONE Add textarea to join details to demand
    Project
      * Copy a project
      * DONE Check an already project name existed
      * DONE List of files uploaded
      * DONE Check if file exists
      * Bug: Private project edition: project manager can be transfered to all users
      * DONE Add file (picture or resource) with 
            * http://localhost/tangara/project/1/12/upload
    Document
      * check if exists Path property

- Files upload
    * php files couldn't be loaded
    * DONE File has to be linked to a project
    * DONE Complete "check" function
    * Clean filename & manage upload cycles
    * DONE Copy file in directory
    * BlueImp/DropZone: jQuery Upload file
    * DONE files upload in directory configured in paramaters.yml
    * DONE Tangara directory configured in paramaters.yml
    * Javascript: popup to preview file clicked
        * text/js http://codecanyon.net/item/jquery-document-viewer/1732515
        * picture http://www.yoxigen.com/yoxview/

- Security
    * Re-plug security context (unplugged for dev) (régis)

- Admin interface
    * User management : add/remove/changepassd
    * Grp management : add/remove/rename/modify
    * Project management : add/remove/rename/modify/move
    * DONE Include SlickGrid
    * User s informations managed by Slickgrid
        * id
        * username
        * password

    * DONE make a javascript tool to activate the current item clicked

- All pages translated in en/fr 

- Tests

- Script 
    * DONE DEPRECATED After a modif on schema update
    * Code tidy: all javascripts in js/vendors

- Request
    * DONE identical private/group project
    * DONE GetLocale
        * http://localhost/tangara/locale
    * Create a tgr file
        * http://localhost/tangara/project/1/12/addTgr
            * Unexist file
               * {"error": "file"}

    * DONE Get file content
        * http://localhost/tangara/project/1/12/getContent?filename=fichier.tgr
            * error: unowned
    * DONE Get file Programs list 
        * http://localhost/tangara/project/1/12/getProgramList
    * DONE Get resources list
        * http://localhost/tangara/project/1/12/getResources
    * DONE Remove file -> en POST remove: 'file.tgr'
        * http://localhost/tangara/project/1/12/remove
        * success -> removed: 'file.tgr'
        * error unremoved: 'file.tgr'

    * Open a file

- Installation page 
    * A configuration page could be used to install Tangara-UI (DB settings, etc.)

- Code cleaning
    * User/Group/Project functions in Managers

Priority
-------------

  * Clean file upload
  * Delete crap system (category for group)
  * Security system re plug

