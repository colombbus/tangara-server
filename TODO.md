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
    Group
      * DONE User could have private group for managing private projects
      * DONE Edition
      * DONE Add upload
      * Requested groups list on profile page
      * DONE Group manager mail confirmation sending
      * DONE Group template: list of members
      * Add textarea to join details to demand
    Project
      * Copy a project
      * (ajax) Check an already project name existed
      * DONE List of files uploaded
      * Check if file exists
      * bug: Private project edition: project manager can be transfered to all users

- Files upload
    * DONE File has to be linked to a project
    * Complete "check" function
    * Copy file in directory
    * BlueImp/DropZone: jQuery Upload file
    * files upload in directory configured in paramaters.yml
    * javascript: popup to preview file clicked

- Security
    * re-plug security context (unplugged for dev)

- Admin interface
    * user management : add/remove/changepassd
    * grp management : add/remove/rename/modify
    * project management : add/remove/rename/modify/move
    * user's informations managed by Slickgrid

- Tests

- Script 
    * DEPRECATED After a modif on schema update

- Request
    * Projet personnel ou de groupe sont identiques niveau URL mais il y a un champ "perso" dans la table
    * GetLocale
        http://localhost/tangara/locale
    * Ask file deletion
        http://localhost/tangara/project/12/remove?filename=fichier.tgr
    * Create a tgr file
        http://localhost/tangara/project/12/addTgr
    * Get file content
        http://localhost/tangara/project/12/getContent?filename=fichier.tgr
    * Ajouter un fichier (image ou ressource) manuellement
        http://localhost/tangara/project/12/upload
    * Get file Programs list
        http://localhost/tangara/project/12/getProgramList
    * Get resourceObtenir la liste des ressources
        http://localhost/tangara/project/12/getResources

- Installation page 
    * A configuration page could be used to install Tangara-UI (DB settings, etc.)


Priority
-------------




