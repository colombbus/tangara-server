TANGARA-UI TODO LIST
==========

- Project
  * auto detect locale 
      User
          * DONE Selector menu for ProjectOwnerGroup that contains groups of the user
          * DONE Create a group in the same time user is created
          * DONE user shouldn't have projects
      Group
          * DONE User could have private group for managing private projects
          * Edition
          * Add upload
          * Requested groups list on profile page
          * Group manager mail confirmation sending
          * DONE Group template: list of members
          * Add textarea to join details to demand
      Project
          * Copy a project
          * (ajax) Check an already project name existed
          * List of files uploaded

- Files upload
      * DONE File has to be linked to a project
      * Complete "check" function
      * Copy file in directory
      * BlueImp/DropZone: jQuery Upload file
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
      http://localhost/tangara/project/12?remove=fichier.tgr
  * Create a tgr file
      http://localhost/tangara/project/12?add=fichier.tgr
  * Get file content
      http://localhost/tangara/project/12?content=fichier.tgr
  * Ajouter un fichier (image ou ressource) manuellement
      http://localhost/tangara/project/upload
  * Get file list
      http://localhost/tangara/project/12?action=programs
  * Get resourceObtenir la liste des ressources
      http://localhost/tangara/project/12?action=resources

- Installation page 
  * A configuration page could be used to install Tangara-UI (DB settings, etc.)
