#!/bin/bash

#TODO: REWRITE IT IN NODE JS OR PHP

rsync -av --delete AdminLTE/dist/* ../public/assets/admin-lte/
rsync -av --delete AdminLTE/plugins ../public/assets/admin-lte/
rsync -av --delete ionIcons/docs/* ../public/assets/admin-lte/ion-icons
rsync groceryCrud/app/Libraries/GroceryCrud.php ../app/Libraries/
#rsync groceryCrud/app/Config/GroceryCrud.php ../app/Config/
rsync groceryCrud/app/Models/GroceryCrudModel.php ../app/Models/
rsync -av groceryCrud/public/assets/grocery_crud ../public/assets/
