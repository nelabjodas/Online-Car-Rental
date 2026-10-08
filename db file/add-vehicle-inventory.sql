-- Run this once on an existing carrental database before using the inventory feature.
ALTER TABLE `tblvehicles`
  ADD COLUMN `AvailableQuantity` int(11) NOT NULL DEFAULT 0 AFTER `SeatingCapacity`,
  ADD COLUMN `PopularityCount` int(11) NOT NULL DEFAULT 0 AFTER `AvailableQuantity`;
