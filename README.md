# Bingus Music Shop

![](https://img.shields.io/badge/Lines_of_PHP-3883-blue)
![](https://img.shields.io/badge/Total_Lines_of_Code-5270-blue)

A modern web application built using PHP with the MVC pattern and MySQL for a musical instrument retailer. Made for the Level 5 Web Programming Unit at Bournemouth University. The website is currently hosted on the university's server at [https://s5411045.bucomputing.uk/BingusMusicShop.php](https://s5411045.bucomputing.uk/BingusMusicShop.php).

![Example Screenshot](https://github.com/joeblakeb/BingusMusicShop/blob/main/documentation/Screenshot.png?raw=true)

The [Self Reflection](https://github.com/JoeBlakeB/BingusMusicShop/blob/main/documentation/Self-Reflection.md) is in the `documentation` directory and the lab and tutorial tasks are available in my [WebProgramming2022](https://github.com/JoeBlakeB/WebProgramming2022) repository. 

## SQL Database Setup

### Database Structure

The database structure must be manually created before using the website. The CREATE script is in the `databaseStructure.sql` file in the `model` directory. It also incldes a command to create the admin user. The DROP commands are commented out at the bottom of the file. An entity relationship diagram for the structure is available in the `documentation` directory as [DatabaseERD.drawio.svg](https://github.com/joeblakeb/BingusMusicShop/blob/main/documentation/DatabaseERD.drawio.svg?raw=true).

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

## References
  - Font Awesome 6.2.0, available at [FontAwesome.com](https://fontawesome.com/), [License](https://github.com/JoeBlakeB/BingusMusicShop/blob/main/static/fontawesome/LICENSE.txt).
  - FredokaOne Font, by Milena Brandao, available at [Google Fonts](https://fonts.google.com/specimen/Fredoka+One), [License](https://github.com/JoeBlakeB/BingusMusicShop/blob/main/static/fonts/FredokaOne.txt).
  - Open Sans Font, by Steve Matteson, available at [Google Fonts](https://fonts.google.com/specimen/Open+Sans), [License](https://github.com/JoeBlakeB/BingusMusicShop/blob/main/static/fonts/OpenSans.txt).
  - Ubuntu Font, by Dalton Maag, available at [Google Fonts](https://fonts.google.com/specimen/Ubuntu), [License](https://github.com/JoeBlakeB/BingusMusicShop/blob/main/static/fonts/UbuntuBold.txt).
  - Countries Array, by html-code-generator.com, available at [html-code-generator.com](https://www.html-code-generator.com/php/array/country-names)

Copyright (c) 2022 JoeBlakeB (Joe Baker), All Rights Reserved.