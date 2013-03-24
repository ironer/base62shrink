base62shrink
============

Short javascript to perform LZW compression on longer structured or repetitive UTF8 data to some
universally web safe form.

Demo: http://b62s.ironer.cz

***

Used sources:
- LZW compression: http://rosettacode.org/wiki/LZW_compression#JavaScript
- UTF8 encode & decode: http://ecmanaut.blogspot.cz/2006/07/encoding-decoding-utf8-in-javascript.html

***

Base62shrink uses LZW compression and some javascript data transformations to prevent
wasting of bit information and simply encode the resulting LZW array of indexes to base62
string of safe chars (a-zA-Z0-9). Thus on some data examples makes the base62 encoded data
shorter instead of 33% increase of standard base64 encode function.

Maybe is the same principle used in some compressions, but I couldn't find anything short
and simple.

***

If anyone ever wonders, how the code works, here is some short description:

1) UTF8 encoded string is compressed by LZW to array of integer indexes in dictionary of concatenated
   substrings.

2) The integers in array are going to be compressed and each of them encoded to base6, then whole array
   converted to string with separator and minus sign effectively replaced by '6' and '7' to get base8 string.

   - The bit length of array of integers is compressed by replacing every element except 1st one by
      the integer calculated as delta from previous element.

   - The deltas in array are changed from base10 to base6 for further compression.

   - The array is joined to string by char '6' as separator.
   
   - Every occurrence of '6' separator before negative delta in base6 is replaced by '7' and the minus sign
      is removed.

3) Resulting base8 string from step 2 is going to be encoded to base62. 2 chars to 1 char.

   - Additional base8 value is added at the start of the string as flag of redundant char added at the end.
      Thus odd length string has just the flag value at the start added to give even length, while even length
      string has different flag value at the start and redundant value at the end to give even length.

   - The number 7 represents separator followed by minus in the base8 string, thus it won't be ever directly
      followed by 6 or 7 (separator or separator with minus). That's why highest value of 2 directly following
      chars in the base8 string doesn't go over 61 in base10 and used base62 is enough.

   - The encoding to base62 is pretty straightforward as the base8 string is even length now and each pair
      of following chars represents one of 62 values.

4) As mentioned above, the number 7 is never followed by 6 or 7. This works vice versa. So there will be never
   '66' or '67' in the base8 string, which leaves characters at index 54 and 55 ('2' and '3') from base62
   encoding string unused. The combination '70' in base8 string is impossible too, as it would stand for:
   separator followed by minus sign followed by zero, so the character at index 56 ('4') won't appear
   in the resulting base62 string either. Thus characters '2', '3' and '4' can be used as separators or eof
   for encoded strings.
   
***

Maybe someone finds this script useful ;-)
