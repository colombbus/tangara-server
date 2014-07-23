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
      * Requested groups list on profile page
      * DONE Group manager mail confirmation sending
      * DONE Group template: list of members
      * DONE Add textarea to join details to demand
    Project
      * Copy a project
      * DONE Check an already project name existed
      * DONE List of files uploaded
      * Check if file exists
      * Bug: Private project edition: project manager can be transfered to all users
      * DONE Add file (picture or resource) with 
            http://localhost/tangara/project/1/12/upload

- Files upload
    * DONE File has to be linked to a project
    * Complete "check" function
    * DONE Copy file in directory
    * BlueImp/DropZone: jQuery Upload file
    * DONE files upload in directory configured in paramaters.yml
    * DONE Tangara directory configured in paramaters.yml
    * Javascript: popup to preview file clicked
        text/js http://codecanyon.net/item/jquery-document-viewer/1732515
        picture http://www.yoxigen.com/yoxview/

- Security
    * Re-plug security context (unplugged for dev) (régis)

- Admin interface
    * User management : add/remove/changepassd
    * Grp management : add/remove/rename/modify
    * Project management : add/remove/rename/modify/move
    * User s informations managed by Slickgrid
    * DONE make a javascript tool to activate the current item clicked

- All pages translated in en/fr 

- Tests

- Script 
    * DONE DEPRECATED After a modif on schema update
    * Code tidy: all javascripts in js/vendors

- Request
    * DONE identical private/group project
    * DONE GetLocale
        http://localhost/tangara/locale
    * Create a tgr file
        http://localhost/tangara/project/1/12/addTgr
    * Get file content
        http://localhost/tangara/project/1/12/getContent?filename=fichier.tgr
    * Get file Programs list
        http://localhost/tangara/project/1/12/getProgramList
    * Get resourceObtenir la liste des ressources
        http://localhost/tangara/project/1/12/getResources
    * Remove file -> en POST filename: 'file.tgr'
        http://localhost/tangara/project/1/12/remove


- Installation page 
    * A configuration page could be used to install Tangara-UI (DB settings, etc.)


Priority
-------------




