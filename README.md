__Phaiku__ is a fast and simple but extensible static website manager based on the 
micro framework [Slim](http://www.slimframework.com/). It works without databases 
and there's no administration section. You can update the site using ftp, sftp or 
preferably [git](http://git-scm.com/).

PHaiku is at this stage a proof of concept, meant to be played with, explored and 
extended. It has a very brief source code, that's ought to be understandable.

The name PHaiku is obviously derived from PHP and haiku, the later being a short 
poetry form of Japanese origin, consisting of only five verses with respectively 
5 - 7 - 5 syllabes. Read more about haiku on [Wikipedia](http://en.wikipedia.org/wiki/Haiku).

## Download and install

To install PHaiku, use git to clone it to your public folder. 

```

git clone https://github.com/gresakg/PHaiku.git myproject

```		

Alternatively, you can download the zip package and unzip it to your public folder.

After that, please install dependencies by running 

```

composer install

```

If you don't have composer installed (yet), [install it](https://getcomposer.org/doc/01-basic-usage.md#installation) 
and then install dependencies as described [here](https://getcomposer.org/doc/01-basic-usage.md#installing-dependencies)

The fastest way to get up and running is simply by renaming config-sample 
and data-sample folders respectively to config and data. Then just change 
the data and configuration ...

## Demo and support

See the demo, support and documentation at [phaiku.gresak.net](http://phaiku.gresak.net)

## License

Phaiku is released under GPL v.2

Copyright (c) 2014 Gregor Grešak, gresak.net

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
