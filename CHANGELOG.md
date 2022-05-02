# Changelog - oxid 2 afterbuy plugin for OXID6

##1.7.2
Release 2022-05-02
* Fixed stock management on order import
* Fixed fross and net prices when OXID is run in net mode
* Changed product id determination from artnum to Afterbuy id mapping

##1.7.1
Released 2021-10-18
* Refactored address splitting
* changed the decimal separator of the XML weight attribute

##1.7.0
Released 2021-10-11
* Made the module installable with Composer

##1.6.4
Released 2021-08-25
* Fixed value for external article number
* Fixed export of paid amount

##1.6.3
Released 2021-06-01
* Changed the value exported as EAN, as it was using a wrong parameter
* Fixed handling of quotes in article title
* Added export of paid amount in order status export

##1.6.2
Released 2020-08-07
* Fixed article import including categories connections
* Fixed category order
* Fixed price import AB --> OXID
* Added stock update cron
* Added tracking code transfer

## 1.6.1
Released 2020-06-19
* Fixed import of article description

## 1.6.0
Released 2020-04-08
* Added compatibility to OXID 6.2.x

## 1.5.0
Released 2019-12-19
* Added unique logging for OXID5 and OXID6
* Add feature for downloading and resetting logs

## 1.4.0
Released 2019-06-17
* Added option for exporting ordernumber to order in additional field
* Added import option for making sure article will have an article number

## 1.3.1
Released 2019-05-17
* Fixed order submission
* Fixed error assigning categories at import

## 1.3.0
Released 2019-04-25
* Added option for transferring orders in utf8
* Reorganized utf8-handling for order submission

## 1.2.1
Released 2019-03-04
* Fixed category assigning problem
* Fixed folder assignment
* Added automatic migration of afterbuy data of versions prior 1.1.0 while activating

## 1.2.0
Released 2019-02-05
* Added option to determine leading system which flags allowed plugins-actions
* Displaying Afterbuy ProductID in article afterbuy tab
* Added exporting article variants as eBay-Variations
* Overall bugfixes and some code refactoring 

## 1.1.0
Released 2018-11-14
* Changed database structure for not extending existing tables
* Integrated API interface
* Added automatic installer after activation
* Improved code quality

## 1.0.4
Released 2018-01-02
* Added improvements for overloading possibilities
* Some code cleanind

## 1.0.3
Released 2017-12-14
* Improvements for picture-handling
* Linked to newer api version

## 1.0.2
Released 2017-11-28
* Build with external library
* Fixed issue with sending orders

## 1.0.1
Released 2017-10-30
* Fixed problem with orderimportscript (Wrong classname)
* Set sending orders on the fly false per default

## 1.0.0
Released 2017-10-30.
* A brand new OXID plugin has fallen down from the stars to our planet.
* Contains syncing articles to afterbuy, importing and exporting orders, 
  payment-assignments and orderstatus-syncing

