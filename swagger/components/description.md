# Introduction
You've found the sxcu.net API documentation! These pages are dedicated to showing you all the ways that you can use sxcu.net to make cool stuff.
All of our [documentation is on GitHub](https://github.com/MisterFixx/sxcu-docs), so feel free to submuit corrections and improvements!

## Bugs
If you believe you're experiencing a bug with our API or want to report incorrect documentation, open an issue on our [issue tracker](https://github.com/MisterFixx/sxcu-docs/issues).

## Still need some help?
Join the [Official Discord server](https://discord.gg/Yuf6ukX) for support and discussion regarding sxcu.net's APIs.

# API Referance
## Base URL
```text
https://sxcu.net/api
```
## User Agent
Clients using the HTTP API must provide a valid [User Agent](https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.43) which specifies information about the client library and version in the following format:
### User Agent Example
```text
User-Agent: sxcuUploader ($url, $versionNumber)
```
Clients may append more information and metadata to the end of this string as they wish.


## Nullable and Optional Resource Fields
Resource fields that may contain a null value have types that are prefixed with a question mark. Resource fields that are optional have names that are suffixed with a question mark.

### Example Nullable and Optional Fields
FIELD                        | TYPE
-----------------------------|--------
optional_field?              | string
nullable_field               | ?string
optional_and_nullable_field? | ?string

## Boolean Query Strings
Certain endpoints in the API are documented to accept booleans for their query string parameters. While there is no standard system for boolean representation in query string parameters, sxcu.net represents such cases using True, true, or 1 for true and False, false or 0 for false.

## Snowflakes
sxcu.net utilizes a customized version of Twitter's [snowflake](https://github.com/twitter-archive/snowflake/tree/scala_28) format for uniquely identifiable descriptors (IDs).
These IDs are guaranteed to be unique across all of sxcu.net.  
Because Snowflake IDs are up to 53 bits in size (e.g. a uint64), they are always referred by as base 63 encoded strings to prevent integer overflows in some languages and also to make them visually nicer.  
Since Snowflakes are always base 63 encoded, in order to do any bitwise operations on then, they have to first be converted back into base 10.  
**Note**: sxcu.net started employing Snowflakes for its object IDs since the 30th of June, 2021 at 14:40 PM, any ID before that is not a valid Snowflake.

### Snowflake ID Broken Down in Binary
```text
1111111111111111111111111111111 1111 1111 11111111111111
53                              22   18   14            0
```

### Snowflake ID Format Structure (Left to Right)
Field       | Bits     | Number of bits | Description                                                                           | Retrieval
------------|----------|----------------|---------------------------------------------------------------------------------------|---------------------------------
Timestamp   | 53 to 22 | 31 bits        | Seconds since sxcu.net Epoch, which is 1326466131.                                    | `(snowflake >> 22) + 1326466131`
Object type | 21 to 18 | 4 bits         | Indicates the type of object this snowflake belongs to.                               | `(snowflake >> 18 & 15)`
Object flag | 17 to 14 | 4 bits         | Will be 0 unless object type is 1, then it will contain the type of an uploaded file. | `(snowflake >> 14 & 15)`
Sequence    | 13 to 0  | 14 bits        | This number is incremented for every ID that is generated in the same second.         | `snowflake & 16383`

#### Object types field
1 = uploaded file  
2 = redirect (link)  
3 = collection   
4 = paste (used in cancer-co.de)  

#### Object flags field
1 = png  
2 = jpeg  
3 = gif  
4 = ico  
5 = bmp  
6 = tiff  
7 = webm  

### Convert Snowflake to DateTime
![Graphical representation of how a Snowflake is constructed](https://sxcu.net/assets/img/53fMTmLR4.png)

## API error referance
sxcu.net uses internal error codes and an HTTP status code of 400 for any API error, instead of HTTP status codes. This gives us the ability to assign a unique code to each error, instead of having to reuse codes from a narrow selection of HTTP status codes.

### Table of errors
Error | Message                                                                
------|------------------------------------------------------------------------
11    | Title not provided                                                     
12    | Privacy setting not provided                                           
13    | Unlisted parameter not provided                                        
14    | value of 'unlisted' must be boolean                                    
15    | value of 'private' must be boolean                                     
16    | An error occoured while creating the collection, plese try again later 
17    | Title too long                                                         
18    | Description too long                                                  
31    | Subdomain not provided                                                 
41    | Collection ID not provided                                             
42    | Collection token not provided                                          
43    | Rate limit exceeded                                                    
44    | Value of 'unlised' must be boolean                                     
45    | Token mismatch                                                         
46    | Collection not found                                                   
51    | No 'link' parameter sent                                               
52    | URL parameter too long                                                 
71    | Requested file not found                                               
72    | File ID not sent                                                       
801   | File type not allowed                                                  
802   | Upload error 101x                                                      
803   | User-agent header not set                                              
804   | File is over the maximum file size limit                               
805   | File is under the minimum file size limit                              
806   | Malformed JSON in OpenGraph properties                                 
807   | og_properties object is too long                                       
808   | Subdomain is private, a valid upload token is required                 
809   | No upload token                                                        
810   | Invalid collection token                                               
811   | Collection is private, valid collection token must be provided         
812   | Collection not found                                                   
813   | The file was not uploaded due to an unknown error                      
814   | No file sent                                                           
91    | No host parameter sent                                                 
101   | Link not found                                                         
102   | File not found                                                         
103   | Missing object ID or deletion token
301   | Collection not found
302   | No collection ID provided

## Migrating from API v1
The new and old APIs have a few slight differences between them making them incompatible, any existing projects that use the old sxcu.net API would have to migrate before the old API gets completley removed.

### API changes made in v2
#### Global
All endpoints no longer return different status codes for different errors, instead they all return status code 400 with an error explaination in the response.

#### Subdomains
/api?action=domains  
&nbsp;&nbsp;&nbsp;&nbsp;URL changed to [/api/subdomains](#get-/subdomains)  
&nbsp;&nbsp;&nbsp;&nbsp;Field `img_views` changed to `file_views`  
  
[Added a new endpoint](#get-/subdomains/check/-subdomain-)

#### Files
/upload -> URL changed to [api/files/create](#post-/files/create)  
sxcu.net/{ID}.json -> URL changed to [/api/files/{fileId}](#get-/files/-fileId-)  
/d/{fileId}/{deletionToken} -> URL changed to [/api/files/delete/{fileId}/{deletionToken}](#get-/files/delete/-fileId-/-deletionToken-)   

#### Collections
sxcu.net/c/{CollectionId}.json  
&nbsp;&nbsp;&nbsp;&nbsp;URL changed to [api/collections/{collectionId}](#get-/collection/-collectionId-)  
&nbsp;&nbsp;&nbsp;&nbsp;Field `img_views` changed to `file_views`  
&nbsp;&nbsp;&nbsp;&nbsp;Field `images` changed to `files`  
<br>
Collection creation endpoint URL changed to [api/collections/create](#post-/collections/create)  
Collection edit endpoint URL changed to [api/collections/edit/{collectionId}](#post-/collections/edit/-collectionId-)  

#### Links
/shorten -> URL changed to [api/links/create](#post-/links/create)  
/d/{linkId}/{deletionToken} -> URL changed to [/api/links/delete/{linkId}/{deletionToken}](#get-/links/delete/-linkId-/-deletionToken-)     