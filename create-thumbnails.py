#!/usr/bin/python

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