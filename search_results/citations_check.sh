#!/bin/bash

awk -v RS="" '
{
    original=$0

    # Rimuove TUTTO il contenuto tra parentesi tonde (anche multilinea)
    cleaned = original
    while (cleaned ~ /\([^()]*[0-9]+-[0-9]+-[0-9]+-[0-9]+-[0-9][^()]*\)/) {
        gsub(/\([^()]*[0-9]+-[0-9]+-[0-9]+-[0-9]+-[0-9][^()]*\)/, "", cleaned)
    }

    # Conta le parole prima e dopo
    n_original = split(original, a, /[ \n\t]+/)
    n_clean    = split(cleaned,  b, /[ \n\t]+/)

	if(n_clean>=100){
    print "----------------------------------"
    print "TOKEN:"
    print cleaned
    print "\nWord count (clean): " n_clean
    print "Words removed:      " (n_original - n_clean)
    print "----------------------------------\n"}
}
' $1

