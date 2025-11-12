#!/bin/bash

# Recursive globbing activation
shopt -s globstar

# Create the list with all the books
echo "Creating the books list..."
for f in ~/programming/php/**/*.pdf; do basename "$f" .pdf; done > all_books.txt

# Iterate through csv files to generate the txt files
echo "Iterating through csv files to generate the txt files and the searched tokens..."
for f in *.csv; do
searched_token="${f#SearchResults_}"
searched_token="${searched_token%.csv}"
./isbn_processing.awk -v token="${searched_token}" ${f} > ${f%.csv}.txt
done

# Iterate through txt files to generate the filtered txt files
echo "Iterating through csv files to generate the filtered txt files..."
for f in SearchResults_*.txt; do
grep -Ff all_books.txt ${f} >> Filtered${f}
done

# Cat all the txt files together besides "all_books.txt" and "token_cercati.txt"
# Preserve the header
echo "Re-generating the header..."
header_file=$(ls -1 SearchResults_*.txt | awk 'NR==1')
header=$(awk 'NR==1' ${header_file})
echo "Concatenating all the filtered files together..."
cat FilteredSearchResults_*.txt > analytical_index.txt
echo "Inserting the header..."
sed -i "1i ${header}" analytical_index.txt
echo "Pretty printing the analytical index according to a separator..."
column -t -s $"|" analytical_index.txt > formatted_analytical_index.txt

# Delete useless files
echo "Deleting useless files..."
rm *SearchResults_*.txt
echo "Deleting the analytical index in its pre-formatted form..."
rm analytical_index.txt
