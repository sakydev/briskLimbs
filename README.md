A video sharing software created with both webmasters and developers in mind.
![BriskLimbs Home](http://brisklimbs.com/demo.png)

## Table of contents

- [Why BriskLimbs](#why-brisklimbs)
- [Getting Started](#getting-started)
- [Requirments](#requirments)
- [Recomendations](#recomendations)
- [Installation](#installation)
- [Documentation](https://github.com/briskLimbs/briskLimbs/wiki)
- [Official Addons](#official-addons)
- [Frequently Asked Questions](#faq)
- [Contribute](#contribute)
- [Directory Structure](#directory-structure)
- [Videos Examples](https://github.com/briskLimbs/briskLimbs/wiki/Class:-Videos)
- [Users Examples](https://github.com/briskLimbs/briskLimbs/wiki/Class:-Users)
- [Addons Examples](https://github.com/briskLimbs/briskLimbs/wiki/Class:-Addons)
- [Thumbnails Exmaples](https://github.com/briskLimbs/briskLimbs/wiki/Class:-Thumbnails)
- [Files Examples](https://github.com/briskLimbs/briskLimbs/wiki/Class:-Files)
- [Errors Examples](https://github.com/briskLimbs/briskLimbs/wiki/Class:-Errors)
- [License](#license)
- [Credits](#credits)

### Why BriskLimbs
There aren't many open source video solutions out there. The ones that exist are mostly painful to deal with. The frustration of having to go back and forth with customer support, lack of documentation and having to figure things out yourself, claiming features that aren't even half built and lots of similiar issues is the reason why we created BriskLimbs.

We have the detremination to always put the community first and build things people actually want. We aren't hiding behind open source curtain looking at your wallets and instead want to share solutions with the world in purest sense of open source. 

### Getting Started
It is easy to get started with BriskLimbs no matter if you are a developer or a webmaster with no technical knowledge. 

#### Requirments
briskLimbs requires several tools to run smoothly. You are recomended to have them all ready before installation.

- [PHP 7](http://php.net/downloads.php) or higher
- [MySQL 5.5](https://dev.mysql.com/downloads/mysql/5.7.html) or higher
- [Twig 2](https://twig.symfony.com) or higher
- [FFMPEG](https://www.ffmpeg.org/) with libfdk_aac and x264
- [FFPROBE](https://ffmpeg.org/ffprobe.html)
- [Bootstrap 4](https://getbootstrap.com/docs/4.0/getting-started/introduction/)
- [Git](https://git-scm.com/)
- [Composer](https://getcomposer.org/)

#### Recomendations
- OS: [Ubuntu](https://www.ubuntu.com/) or [Centos](https://www.centos.org/)
- Server: VPS or Dedicated
- Memory: 8GB or higher
- Space: Completely depends on your usage
- PHP max_execution_time: 5400 or higher
- PHP upload_max_filesize: 10 - 20% higher than your largest uploads

#### Installation
Once you have installed and configured all required tools you can begin installing briskLimbs.

###### Latest Stable Version
```
wget https://github.com/briskLimbs/briskLimbs/archive/v1.0.1.zip
unzip briskLimbs-1.0.zip
cd briskLimbs-1.0
composer require "twig/twig:^2.0"
```

###### Latest Development Version
```
git clone https://github.com/briskLimbs/briskLimbs.git
cd briskLimbs
composer require "twig/twig:^2.0"
```

That's it. Now visit "http://{yourhost}/briskLimbs" in your browser and follow configuration instructions. 

#### Official Addons
Addons allow you to extend basic functionality of BriskLimbs by adding new features, enhancing existing ones or simply making something easier. 

- [Pages](https://github.com/briskLimbs/pages): Add, manage and display custom pages
- [TagsDip](https://github.com/briskLimbs/tagsDip): Add custom CSS, JavaScript or any other tags in Header or Footer of website
- [TwitFeed](https://github.com/briskLimbs/twitFeed): Show Twitter timeline anywhere
- [ViewsRandomizer](https://github.com/briskLimbs/viewsRandomizer): Randmoize video views easily
- [Facebook Comments](https://github.com/briskLimbs/facebookComments): Add Facebook comments widget

#### FAQ
- Is briskLimbs free?

Yes, briskLimbs is one hundered percent free to use for any of your personal or commercial projects. We may introduce premium addons in future but this software will always be free.

- How do I request custom features?

You can send an email at brisklimbs@gmail.com and our team will be in touch within 24 hours.

- How do I install a new skin?

Extract contents of downloaded zip and then login to your server via Filezilla or similar FTP program and upload extracted contents under */skins* directory. Once finish simply refresh your skins manager and now you should see your addon.

- How do I install a new addon?

Extract contents of downloaded zip and then login to your server via Filezilla or similar FTP program and upload extracted contents under */addons*directory. Once finish simply refresh your addons manager and now you should see your addon.

### Contribute
[Coding Conventions](https://github.com/briskLimbs/briskLimbs/wiki/Coding-Style-Guide)

[Addons: Development & usage](https://github.com/briskLimbs/briskLimbs/wiki/Hello-World-Addon)

[Skins: Development & usage](https://github.com/briskLimbs/briskLimbs/wiki/Hello-World-Skin)

#### Directory structure
```
├── addons                          # all addons are placed here
│   ├── developerTools              # addon for making developer tools available for admins
│   │   ├── developerTools.php      # main file for addon
│   │   ├── install.php             
│   │   ├── pages                   
│   │   │   ├── configs.php
│   │   │   ├── info.php
│   │   │   └── requirements.php
│   │   └── plugin.json             # file responsible for defining an addon
│   └── geoLocation                 # addon for tracking user location
│       ├── functions.php
│       ├── install.php             
│       ├── location.php            # main file addon
│       └── plugin.json             # file responsible for defining an addon
├── admin                           # admin dashboard files
│   └── skins                       # admin skins are placed here
│       └── default                 # holds backend and frontend files for skin
│           ├── addons.php          # admin addons manager
│           ├── blank.php           
│           ├── index.php           # admin dashboard
│           ├── page.php            # blank page used for embeding pages requested by addons
│           ├── settings.php        # admin settings
│           ├── skeleton            # holds frontend files for admin skin
│           │   ├── addons.html
│           │   ├── blank.html
│           │   ├── dashboard.html
│           │   ├── layout.html
│           │   ├── settings.html
│           │   ├── signin.html
│           │   ├── users.html
│           │   └── videos.html
│           ├── users.php           # admin users manager             
│           └── videos.php          # admin videos manager
├── config.php                      # initializes core of briskLimbs
├── configs                         # holds configuration files
│   ├── configs.php                 # holds static overall configurations 
│   ├── constants.php               # defines base constants  
│   ├── db.php                      # holds database credentials, created on installation
│   └── db.sample.php               
├── daemons                         # these are files used for running background processes
│   └── conversion.php              # handles entire conversion process of a video
├── documentation                   # documentation files
│   ├── addons.md
│   ├── skins.md
│   ├── users.md
│   └── videos.md
├── helpers                         # helper functions that speed things up
│   ├── devFunctions.php            # functions purely for easing up development process
│   ├── functions.php               # general functions
│   └── videoFunctions.php          # video related functions
├── index.php                       # all requests land on this page and get routed
├── installer                       # handles installation of briskLimbs
│   ├── assets                      # files used for styling UI
│   │   ├── bootstrap.min.css   
│   │   └── signin.css
│   ├── functions.php               # main functionality of installer 
│   ├── imports                     # sql files to be imported at start
│   │   ├── addons.sql
│   │   ├── settings.sql
│   │   ├── users.sql
│   │   └── videos.sql
│   ├── install.php                 # main file for handling installation
│   ├── pages                       # sub pages for handling installation sections
│   │   ├── checks.php              # runs requirement checks 
│   │   ├── finish.php              # runs final updates
│   │   ├── footer.php
│   │   ├── header.php
│   │   ├── import.php              # imports sql files
│   │   └── release.php             # displays release information
│   ├── release.json                # holds release information
│   └── requirments.json            # holds requirements
├── media                           # directory where all media is stored
│   ├── avatars                     # stores user avatars
│   │   ├── admin.jpg
│   │   └── default.svg
│   ├── logs                        # stores video logs
│   ├── temporary                   # stores video while it is being processed
│   ├── thumbnails                  # stores video thumbnails
│   └── videos                      # stores videos after they have been processed
├── model                           # holds core classes
│   ├── Actions.php                 # handles tracking actions
│   ├── Addons.php                  # all addon related actions
│   ├── Conversion.php              # video conversion and thumbnails generation
│   ├── Database.php                # mysql wrapper in PHP
│   ├── Errors.php                  # handles errors globally
│   ├── Files.php                   # handles scanning, moving and deletion of files
│   ├── Limbs.php                   # handles Twig and performs as bridge between model and view
│   ├── Settings.php                # handles fetching and updating of website settings
│   ├── Thumbnails.php              # handles scanning, moving and deletion of thumbnails
│   ├── User.php                    # wrapper functions for a single user
│   ├── Users.php                   # users related actions
│   ├── Video.php                   # wrapper functions for a single video
│   └── Videos.php                  # videos related actions
├── README.md
├── skins                           # holds frontend skins
│   └── ivar                        # default skin 
│       ├── 404.php
│       ├── assets
│       │   ├── css
│       │   │   ├── custom.css
│       │   │   ├── iconfonts
│       │   ├── fonts
│       │   │   └── Ubuntu
│       │   ├── images
│       │   └── js
│       │       ├── plupload.full.min.js
│       │       ├── upload.js       # handles main video uploader
│       │       ├── video-js        # handles main video player
│       │       └── watch.js        # handles actions on watch page
│       ├── index.php
│       ├── search.php
│       ├── signin.php
│       ├── signout.php
│       ├── signup.php
│       ├── skeleton                # frontend files for default skin
│       │   ├── blank.html
│       │   ├── bricks              # reusable blocks of code
│       │   │   ├── player.html
│       │   │   ├── trending.html
│       │   │   └── video.html
│       │   ├── home.html
│       │   ├── layout.html
│       │   ├── search.html
│       │   ├── signin.html
│       │   ├── signup.html
│       │   ├── upload.html
│       │   └── watch.html
│       ├── upload.php
│       ├── videos.php
│       └── watch.php
└── utils                           # holds additional utility files
```

### License
BriskLimbs is available under MIT License. You can [read more here](https://github.com/briskLimbs/briskLimbs/blob/master/LICENSE)

### Credits
- [PHP MySQLi Database Class](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class) [GNU License](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class/blob/master/LICENSE)
- [Purple Admin](https://github.com/BootstrapDash/PurpleAdmin-Free-Admin-Template) [MIT License](https://opensource.org/licenses/MIT)
