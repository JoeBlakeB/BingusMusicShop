# Bingus Music Shop

A modern web application using PHP and MySQL for A musical instrument retailer. Made for the Level 5 Web Programming Unit at Bournemouth University.

The lab and tutorial tasks are available [here](https://github.com/JoeBlakeB/WebProgramming2022).

## SQL Database Setup

### Database Structure

The database structure must be manually created before using the website. The CREATE script is in the `databaseStructure.sql` file in the `model` directory. It also incldes a command to create the admin user. The DROP commands are commented out at the bottom of the file.

### Database Credentials

The credentials for accessing a MySQL database should be stored in a file called `databaseCredentials.json` in inside the `model` directory in the following format:

```json
{
    "database": "",
    "hostname": "",
    "username": "",
    "password": "",
    "port":     0
}
```

## Refrences
  - Font Awesome 6.2.0, available at [FontAwesome.com](https://fontawesome.com/), [License](https://github.com/JoeBlakeB/BingusMusicShop/blob/main/static/fontawesome/LICENSE.txt).
  - FredokaOne Font, by Milena Brandao, available at [Google Fonts](https://fonts.google.com/specimen/Fredoka+One), [License](https://github.com/JoeBlakeB/BingusMusicShop/blob/main/static/fonts/FredokaOne.txt).

Copyright (c) 2022 JoeBlakeB (Joe Baker), All Rights Reserved.