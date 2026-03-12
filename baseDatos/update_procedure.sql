IF OBJECT_ID('ObtenerPedidosDelMes', 'P') IS NOT NULL
    DROP PROCEDURE ObtenerPedidosDelMes;
GO

CREATE PROCEDURE ObtenerPedidosDelMes
AS
BEGIN
    SELECT * FROM Pedidos
    WHERE MONTH(Fecha) = MONTH(GETDATE())
      AND YEAR(Fecha) = YEAR(GETDATE());
END;
GO