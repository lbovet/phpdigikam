#!/usr/bin/python

## Copyright 2011 Mike Willis (http://blogs.warwick.ac.uk/mikewillis/contact/)
##                Laurent Bovet <laurent.bovet@windmaster.ch>
##
##  This file is part of phpdigikam
##
##  It has been gratefully stolen from:
##  http://blogs.warwick.ac.uk/mikewillis/entry/generating_freedesktoporg_spec/
##
##  phpdigikam is free software; you can redistribute it
##  and/or modify it under the terms of the GNU General
##  Public License as published by the Free Software Foundation;
##  either version 2, or (at your option)
##  any later version.
##  
##  This program is distributed in the hope that it will be useful,
##  but WITHOUT ANY WARRANTY; without even the implied warranty of
##  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
##  GNU General Public License for more details.

import gnome.ui
import gnomevfs
import time
import os
import os.path
import sys

dir=sys.argv[1]

for subdir, dirs, files in os.walk(dir):
 for file in files:
  uri=gnomevfs.get_uri_from_local_path(subdir+"/"+file)  
  mime=gnomevfs.get_mime_type(subdir+"/"+file)
  mtime=int(time.strftime("%s",time.localtime(os.path.getmtime(subdir+"/"+file))))
  thumbFactory = gnome.ui.ThumbnailFactory(gnome.ui.THUMBNAIL_SIZE_LARGE)
  if not os.path.exists(gnome.ui.thumbnail_path_for_uri(uri, gnome.ui.THUMBNAIL_SIZE_LARGE)) and thumbFactory.can_thumbnail(uri ,mime, 0):
      print "Generating for "+uri
      thumbnail=thumbFactory.generate_thumbnail(uri, mime)
      if thumbnail != None:                    
          thumbFactory.save_thumbnail(thumbnail, uri, mtime) 
  else:
      print "Skip "+uri