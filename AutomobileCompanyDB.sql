CREATE DATABASE AutomobileCompanyDB;
USE AutomobileCompanyDB;

-- Company table
CREATE TABLE Company (
    companyID INT PRIMARY KEY,
    name VARCHAR(100)
);

-- Brand table
CREATE TABLE Brand (
    brandID INT PRIMARY KEY,
    name VARCHAR(100),
    companyID INT,
    FOREIGN KEY (companyID) REFERENCES Company(companyID)
);

-- Model table
CREATE TABLE Model (
    modelID INT PRIMARY KEY,
    name VARCHAR(100),
    brandID INT,
    FOREIGN KEY (brandID) REFERENCES Brand(brandID)
);

-- Plant table
CREATE TABLE Plant (
    plantID INT PRIMARY KEY,
    location VARCHAR(100)
);

-- Supplier table
CREATE TABLE Supplier (
    supplierID INT PRIMARY KEY,
    name VARCHAR(100)
);

-- Part table
CREATE TABLE Part (
    partID INT PRIMARY KEY,
    name VARCHAR(100),
    supplierID INT,
    plantID INT,
    FOREIGN KEY (supplierID) REFERENCES Supplier(supplierID),
    FOREIGN KEY (plantID) REFERENCES Plant(plantID)
);

-- Dealer table
CREATE TABLE Dealer (
    dealerID INT PRIMARY KEY,
    name VARCHAR(100),
    location VARCHAR(100)
);

-- Customer table
CREATE TABLE Customer (
    customerID INT PRIMARY KEY,
    name VARCHAR(100),
    address VARCHAR(200),
    phone VARCHAR(20),
    gender CHAR(1),
    income DECIMAL(10,2)
);

-- Vehicle table
CREATE TABLE Vehicle (
    VIN VARCHAR(20) PRIMARY KEY,
    modelID INT,
    color VARCHAR(50),
    engine VARCHAR(50),
    transmission VARCHAR(50),
    bodyStyle VARCHAR(50),
    plantID INT,
    dealerID INT,
    inventoryDate DATE,
    status VARCHAR(20),
    customerID INT,
    saleDate DATE,
    price DECIMAL(10,2),
    FOREIGN KEY (modelID) REFERENCES Model(modelID),
    FOREIGN KEY (plantID) REFERENCES Plant(plantID),
    FOREIGN KEY (dealerID) REFERENCES Dealer(dealerID),
    FOREIGN KEY (customerID) REFERENCES Customer(customerID)
);

-- DealerBrand junction table (M:N relationship between Dealer and Brand)
CREATE TABLE DealerBrand (
    dealerID INT,
    brandID INT,
    PRIMARY KEY (dealerID, brandID),
    FOREIGN KEY (dealerID) REFERENCES Dealer(dealerID),
    FOREIGN KEY (brandID) REFERENCES Brand(brandID)
);

-- ModelPart junction table (M:N relationship between Model and Part)
CREATE TABLE ModelPart (
    modelID INT,
    partID INT,
    PRIMARY KEY (modelID, partID),
    FOREIGN KEY (modelID) REFERENCES Model(modelID),
    FOREIGN KEY (partID) REFERENCES Part(partID)
);

-- Insert into Company (2 entries)
INSERT INTO Company (companyID, name) VALUES
(20, 'Honda Motor Co.'),
(21, 'General Motors');

-- Insert into Brand (3 entries)
INSERT INTO Brand (brandID, name, companyID) VALUES
(20, 'Honda', 20),
(21, 'Acura', 20),
(22, 'Chevrolet', 21);

-- Insert into Model (4 entries)
INSERT INTO Model (modelID, name, brandID) VALUES
(20, 'Civic', 20),
(21, 'MDX', 21),
(22, 'Camaro', 22),
(23, 'Accord', 20);

-- Insert into Plant (2 entries)
INSERT INTO Plant (plantID, location) VALUES
(20, 'Marysville, Ohio, USA'),
(21, 'Saitama, Japan');

-- Insert into Supplier (2 entries)
INSERT INTO Supplier (supplierID, name) VALUES
(20, 'Aisin Seiki'),
(21, 'Bosch');

-- Insert into Part (3 entries)
INSERT INTO Part (partID, name, supplierID, plantID) VALUES
(20, 'Fuel Pump', 20, 20),
(21, 'Spark Plug', 21, 21),
(22, 'Suspension Spring', 20, 20);

-- Insert into Dealer (2 entries)
INSERT INTO Dealer (dealerID, name, location) VALUES
(20, 'Honda World', 'New York, NY'),
(21, 'Chevy Central', 'Detroit, MI');

-- Insert into Customer (3 entries)
INSERT INTO Customer (customerID, name, address, phone, gender, income) VALUES
(20, 'Alice Thompson', '101 Elm St, New York, NY', '555-0201', 'F', 68000.00),
(21, 'Robert Wilson', '202 Cedar Dr, Detroit, MI', '555-0202', 'M', 95000.00),
(22, 'Emily Chen', '303 Spruce Ave, Boston, MA', '555-0203', 'F', 72000.00);

-- Insert into Vehicle (3 entries)
INSERT INTO Vehicle (VIN, modelID, color, engine, transmission, bodyStyle, plantID, dealerID, inventoryDate, status, customerID, saleDate, price) VALUES
('JH4DC53008S000020', 20, 'Red', '2.0L I4', 'Manual', 'Sedan', 20, 20, '2024-08-10', 'Available', NULL, NULL, 27000.00),
('JH4DC53008S000021', 21, 'White', '3.5L V6', 'Automatic', 'SUV', 21, 20, '2024-09-15', 'Sold', 20, '2024-10-01', 48000.00),
('JH4DC53008S000022', 22, 'Yellow', '6.2L V8', 'Manual', 'Coupe', 20, 21, '2024-11-01', 'Available', NULL, NULL, 62000.00);

-- Insert into DealerBrand (3 entries)
INSERT INTO DealerBrand (dealerID, brandID) VALUES
(20, 20), -- Honda World sells Honda
(20, 21), -- Honda World sells Acura
(21, 22); -- Chevy Central sells Chevrolet

-- Insert into ModelPart (3 entries)
INSERT INTO ModelPart (modelID, partID) VALUES
(20, 20), -- Civic uses Fuel Pump
(21, 21), -- MDX uses Spark Plug
(22, 22); -- Camaro uses Suspension Spring