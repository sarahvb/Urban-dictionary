This file will explain how to use access the website, how it works,
and simply go into some of the details around it.


______How to start______
Put the whole folder into htdocs in XAMPP, start apache and mysql, and open index.php
in localhost. The database will be created in your localhost phpmyadmin,
along with one row in each of the three tables, that are just for viewing purposes.

_________Users________
Username: Admin
Password: Admin123
I've created one Admin, which is initialized when the database is created.
Log in with the credentials to test the admin functions. To test a regular user (author),
create your own account and log in. The password created will be hashed. 


_________Div comments___________
- When searching, I am using the OR operator instead of AND. I consulted my teacher (Aland)
about this, and he said that everything worked smoothly the way that it is, and that
there was not a problem using OR. Preferrably I would make it possible to click on the
search results, but this has not been implemented due to no specification in the assignment. 

- In terms of the topics and entries on the homepage, I have assumed and interperted that no entries
shall be displayed before the user chooses a topic. When the user has chosen a topic,
the all the topic's entries will be displayed, but the newest one first. To check this, create a new entry. 

- Admin is the only one who can see how many total entries a topic has. Admin can also delete everything, from
topics to entries to users. Users can delete their own entries or topics. Preferrably admin should get a
notification when trying to delete a user that has created topics and entries, but for now there is only 
provided informtion about that, no extra security.


_______File structure____________
The assets folder includes assets such as fonts.
Everything inside the "includes" folder are files that are not visible to users,
they are just files containing only code and functionalities. 

- setUp.php creates the database when first initialized, if it does not already exist.

- Header.php is a page that is included throughout all the pages the users can access/see.
This means that I can keep the session going on all pages, since it is initiated there.

- dbHandler.php contains the code for multiple things, such as register, log in/out,
and creating new topics and entries. It has connect.php included, which is a file
that sets up a connection to the database. 

- userList.php is the list of users only Admin can access to delete users.

- topicsMenu.php contains very little code that only displays the titles from topics in
a select dropdown menu for the users, when they make a new entry in create.php

- Other filenames speak for themselves. 


_______Design_________
I have not put to much weight on the design. For example I use <br> on the forms to
make them look more organized, eventhough I know this using <br> is not best practice.
This assignment is more about the functionalities rather than design :---)


________Fonts__________
They are both free to use, and are downloaded from dafont.com
- Coolvetica.tff is created by Ray Larabie. His homepage is https://typodermicfonts.com/.
- LouisGeorgeCafe.tff is also downloaded from dafont.com. Contactinfo creator: yiningchen23@gmail.com 
Im not sure I would go for this font as the general bodytext due to reading optimalization
if this was a "real" website, but for the purpose of this assignment I will. 
