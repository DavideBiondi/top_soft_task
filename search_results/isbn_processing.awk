#!/usr/bin/awk -f
BEGIN {	FS="," ; 
		#OFS="|" ; 
		print "Chapter_Title|Publication_Title|ISBN|Chapter|Token"}
NR>1 {
		#Remove numeric prefix and forward slash
		gsub(/[0-9][0-9]\.[0-9][0-9][0-9][0-9]\//, "")
		
		#Split field 6 in ISBN and chapter
		split($6, arr, "_")
		isbn = arr[1]
		chapter = arr[2]
		
		#Print formatted fields
		print $1"|"$2"|"isbn"|"chapter"|"token
} 
