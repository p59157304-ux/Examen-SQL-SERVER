CREATE TABLE Productos (
    id_producto INT PRIMARY KEY,
    nombre_producto VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255),
    num_lote VARCHAR(50),
    precio DECIMAL(10, 2),
    cantidad INT,
    fecha_elaboracion DATE,
    fecha_vencimiento DATE,
    id_stock VARCHAR(20)
);

CREATE TABLE Pedidos (
    ID_Pedido INT PRIMARY KEY IDENTITY(1,1),
    Fecha DATE NOT NULL DEFAULT GETDATE(),
    Total DECIMAL(10, 2) NOT NULL,
    id_producto INT,                       
    
    -- Establece la relación con la tabla Productos
    CONSTRAINT FK_Pedido_Producto 
    FOREIGN KEY (id_producto) REFERENCES Productos(id_producto)
);

CREATE TABLE Usuarios (
    ID_Usuario INT PRIMARY KEY IDENTITY(1,1),
    Nombre_Usuario VARCHAR(50) NOT NULL UNIQUE,
    Contrasena VARCHAR(255) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    
    Tipo_Usuario VARCHAR(20) NOT NULL 
        CHECK (Tipo_Usuario IN ('Administrador', 'Lector', 'Editor')),
    
    Fecha_Registro DATETIME DEFAULT GETDATE()
);

INSERT INTO Productos (id_producto, nombre_producto, descripcion, num_lote, precio, cantidad, fecha_elaboracion, fecha_vencimiento, id_stock)
VALUES 
(1, 'Arroz Integral', 'Bolsa de 1kg grano extra', 'L-401', 2.50, 50, '2026-01-10', '2027-01-10', 'STK-001'),
(2, 'Aceite de Oliva', 'Botella de vidrio 500ml', 'L-102', 8.90, 30, '2025-12-05', '2026-12-05', 'STK-002'),
(3, 'Pasta', 'Paquete de sémola 500g', 'L-882', 1.20, 100, '2026-02-15', '2028-02-15', 'STK-003'),
(4, 'Café Molido', 'Tostado medio 250g', 'L-303', 5.50, 45, '2026-03-01', '2027-03-01', 'STK-004'),
(5, 'Leche Entera', 'Tetrapack 1L UHT', 'L-909', 1.10, 120, '2026-03-05', '2026-09-05', 'STK-005'),
(6, 'Atún en Lata', 'En aceite de girasol', 'L-214', 1.80, 80, '2025-11-20', '2029-11-20', 'STK-006'),
(7, 'Harina de Trigo', 'Todo uso 1kg', 'L-551', 0.95, 60, '2026-01-20', '2027-01-20', 'STK-007'),
(8, 'Lentejas Secas', 'Variedad pardina 500g', 'L-707', 1.40, 40, '2026-02-10', '2027-08-10', 'STK-008'),
(9, 'Mermelada de Fresa', 'Frasco de vidrio 350g', 'L-612', 2.30, 25, '2025-12-15', '2026-12-15', 'STK-009'),
(10, 'Sal', 'Fina yodada 1kg', 'L-111', 0.60, 150, '2026-01-05', '2030-01-05', 'STK-010'),
(11, 'Galletas María', 'Pack familiar 800g', 'L-445', 3.20, 35, '2026-02-28', '2027-02-28', 'STK-011'),
(12, 'Jugo de Naranja', '100% fruta sin azúcar', 'L-228', 1.95, 50, '2026-03-08', '2026-06-08', 'STK-012'),
(13, 'Chocolate 70%', 'Tableta artesanal 100g', 'L-339', 3.50, 20, '2026-01-15', '2027-01-15', 'STK-013'),
(14, 'Cereal Maíz', 'Hojuelas tostadas 400g', 'L-990', 2.80, 40, '2026-02-05', '2027-02-05', 'STK-014'),
(15, 'Azúcar Blanca', 'Refinada bolsa 1kg', 'L-667', 1.15, 90, '2026-01-30', '2028-01-30', 'STK-015');

INSERT INTO Pedidos (Fecha, Total, id_producto)
VALUES 
('2024-01-06', 353.94, 7),
('2024-02-19', 329.23, 12),
('2024-02-17', 292.00, 2),
('2024-02-08', 231.29, 10),
('2024-01-13', 27.50, 3),
('2024-01-18', 231.31, 13),
('2024-01-29', 450.78, 1),
('2024-01-16', 37.42, 9),
('2024-01-16', 30.73, 9),
('2024-01-01', 226.77, 7),
('2024-02-18', 462.81, 4),
('2024-01-04', 482.67, 10),
('2024-02-23', 151.51, 2),
('2024-01-04', 308.92, 11);

INSERT INTO Usuarios (Nombre_Usuario, Contrasena, Email, Tipo_Usuario) 
VALUES  
('admin_principal', '123456789', 'admin@example.com', 'Administrador'),
('carlos_lector', 'password123', 'carlos@example.com', 'Lector'),
('maria_editor', 'editor2024', 'maria@example.com', 'Editor'),
('juan_admin', 'admin123456', 'juan@example.com', 'Administrador'),
('laura_lector', 'laura2024', 'laura@example.com', 'Lector');