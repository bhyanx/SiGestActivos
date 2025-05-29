USE [bdGestionLubriseng]
GO
/****** Object:  StoredProcedure [dbo].[sp_GuardarOrdenDeCompra]    Script Date: 29/05/2025 17:43:53 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
-- =============================================
-- Author:		<Author,,Name>
-- Create date: <Create Date,,>
-- Description:	<Description,,>
-- =============================================

ALTER proc  [dbo].[sp_GuardarOrdenDeCompra]
--------------- Datos del Articulo y Datos de Control ------------
	@pidOrdenCompra as numeric(18,0)=null output,
	@pcodEmpleadoComprador as varchar(20)=null,
	@pcodAlmacen as numeric(18,0)=null,
    @pSerieOrden as varchar(5)=null,
	@pNroOrden as varchar(10)=null,
	@pAplicaIGV as numeric(18, 0)=NULL,
	@pTasaIgv as numeric(18,2)=NULL,
	@pAfectaPercepcion as numeric(18,0)=null,
	@pFechaEmisionOrden as varchar(8)=null,
	@pFechaVencimientoOrden as varchar(8)=null,
	--@pFechaAprobacion as varchar(20)=null,
	@pcodTipoOrdenCompra as numeric(18,0)=null,
	@pCodRequerimiento as numeric(18,0)=null,
	@pSerieNroCotizacion as varchar(20)=null,
	@pidMedioPago as numeric(18,0)=null,
	@pcodMoneda as numeric(18, 0)=null,
	@pTipoCambio as varchar(5)=null,
	@pidEntExtProveedor as numeric(18, 0)=null,
	@pidLocacionEntExterna as numeric(18, 0)=null,
	@pidContactoExtProveedor as numeric(18,0)=null,
	@pCorreoEntExtProveedor as varchar(MAX)=null,
	@pidLineaTelefonica as numeric(18,0)=null,
	@pidEntFinanciera as numeric(18,0)=null,
	@pidCtaEntidadExt as numeric(18,0)=null,
	@pcodEmpleadoConsignado as varchar(20)=null,
	@pidEntExtTransportista as numeric(18,0)=null,
	@pidAgenciaDestino as numeric(18,0)=null,
	@pTiempoEntrega as varchar(150)=null,
	@pFechaAproxLlegada as varchar(8)=null,
	@pIdTipoComp as  numeric(18,0)=null,
	@pNroDocumento as varchar(20)=null,
	@pcodTipoEnvio as numeric(18, 0)=null,
	@pAtenciona as varchar(20)=null,
	@pClave as varchar(10)=null,
	@pReferencia as varchar(MAX)=null,
	@pDsctoG1 as numeric(18, 4)=null,
	@pDsctoG2 as numeric(18, 4)=null,
	@pSubTotalSinIgV as numeric(18,4)=null,
	@pTotalIGV as numeric(18,4)=null,
	@pDescuento as numeric(18,4)=null,
	@pPercepcion as numeric(18,4)=null,
	@pOtrosGastos as numeric(18,4)=null,
	@pTotal as numeric(18,4)=null,
	@pObservacionesOrdCompra as varchar(MAX)=null,
	@pObservacionesOrdCompraInterna as varchar(MAX)=null,
	@pEstReg as varchar(1)=null,
	@pUserMod as varchar(20)=null,
	@pidTipoEstOrdCompra as numeric(18,0)=null
as

------------------- ACCION GUARDAR ----------------------------
begin
 	declare @vSgteCorrelativo numeric(18,0)
	declare @vSerieLocal varchar(4)

	if exists(Select * from tTipoEstadoOrdenCompra where idTipoEstOrdCompra= @pidTipoEstOrdCompra and GeneraCorrelativo='1') and exists(Select * from tSerieOrdenCompra where cod_UnidadNeg=(Select codUnidadNeg from tAlmacenEmpresa where codAlmacen=@pcodAlmacen))  and @pNroOrden is null
		begin
			select @vSerieLocal=Serie, @vSgteCorrelativo= (Numero + 1) 
			from tSerieOrdenCompra
			where cod_UnidadNeg=(select distinct codUnidadNeg from tAlmacenEmpresa where codAlmacen=@pcodAlmacen)
				
			Update tSerieOrdenCompra
			set Numero= (Numero + 1) 
			where cod_UnidadNeg=(select distinct codUnidadNeg from tAlmacenEmpresa where codAlmacen=@pcodAlmacen)
			
			set @pSerieOrden=@vSerieLocal
			set @pNroOrden=convert(varchar(10),@vSgteCorrelativo)
			set @pNroOrden=right('00000000'+@pNroOrden,8) -- para completar 9 numeros de posicion 9

		end

	--set @pObservacionesOrdCompra=trim(REPLACE(REPLACE(REPLACE(@pObservacionesOrdCompra,CHAR(9),''),CHAR(10),''),CHAR(13),''))
	--set @pObservacionesOrdCompraInterna=trim(REPLACE(REPLACE(REPLACE(@pObservacionesOrdCompraInterna,CHAR(9),''),CHAR(10),''),CHAR(13),''))

	if @pidOrdenCompra is null
		begin
			BEGIN try
				begin tran	
					INSERT INTO tOrdenDeCompra
                        (codEmpleadoComprador
                        ,codAlmacen
                        ,SerieOrden
                        ,NroOrden
                        ,AplicaIGV
                        ,TasaIgv
                        ,AfectaPercepcion
                        ,FechaEmisionOrden
                        ,FechaVencimientoOrden
                        ,codTipoOrdenCompra
                        ,CodRequerimiento
                        ,SerieNroCotizacion
                        ,idMedioPago
                        ,codMoneda
                        ,TipoCambio
                        ,idEntExtProveedor
                        ,idLocacionEntExterna
                        ,idContactoExtProveedor
                        ,CorreoEntExtProveedor
                        ,idLineaTelefonica
                        ,idEntFinanciera
                        ,idCtaEntidadExt
                        ,codEmpleadoConsignado
                        ,idEntExtTransportista
                        ,idAgenciaDestino
                        ,TiempoEntrega
                        ,FechaAproxLlegada
                        ,IdTipoComp
                        ,NroDocumento
                        ,codTipoEnvio
                        ,Atenciona
                        ,Clave
                        ,Referencia
                        ,DsctoG1
                        ,DsctoG2
                        ,SubTotalSinIgV
                        ,TotalIGV
                        ,Descuento
                        ,Percepcion
                        ,OtrosGastos
                        ,Total
                        ,ObservacionesOrdCompra
                        ,ObservacionesOrdCompraInterna
                        ,EstReg
                        ,UserMod
                        ,FechaMod)
                    VALUES (@pcodEmpleadoComprador
                        ,@pcodAlmacen
                        ,@pSerieOrden
                        ,@pNroOrden
                        ,@pAplicaIGV
                        ,@pTasaIgv
                        ,@pAfectaPercepcion
                        ,@pFechaEmisionOrden
                        ,@pFechaVencimientoOrden
                        ,@pcodTipoOrdenCompra
                        ,@pCodRequerimiento
                        ,@pSerieNroCotizacion
                        ,@pidMedioPago
                        ,@pcodMoneda
                        ,@pTipoCambio
                        ,@pidEntExtProveedor
                        ,@pidLocacionEntExterna
                        ,@pidContactoExtProveedor
                        ,@pCorreoEntExtProveedor
                        ,@pidLineaTelefonica
                        ,@pidEntFinanciera
                        ,@pidCtaEntidadExt
                        ,@pcodEmpleadoConsignado
                        ,@pidEntExtTransportista
                        ,@pidAgenciaDestino
                        ,@pTiempoEntrega
                        ,@pFechaAproxLlegada
                        ,@pIdTipoComp
                        ,@pNroDocumento
                        ,@pcodTipoEnvio
                        ,@pAtenciona
                        ,@pClave
                        ,@pReferencia
                        ,@pDsctoG1
                        ,@pDsctoG2
                        ,@pSubTotalSinIgV
                        ,@pTotalIGV
                        ,@pDescuento
                        ,@pPercepcion
                        ,@pOtrosGastos
                        ,@pTotal
                        ,@pObservacionesOrdCompra
                        ,@pObservacionesOrdCompraInterna
                        ,@pEstReg
                        ,@pUserMod
                        ,GETDATE())	
					set @pidOrdenCompra=SCOPE_IDENTITY()
				commit
				--return @pidOrdenCompra
			END try
			begin catch
				ROLLBACK
				PRINT ERROR_MESSAGE()
				set @pidOrdenCompra=0
			end catch
		end
	else
		begin
			if exists(Select * from tOrdenDeCompra OC  inner join tEstadoOrdenCompra E on OC.idOrdenCompra=E.idOrdenCompra where OC.idOrdenCompra=@pidOrdenCompra and OC.EstReg='1')
			-- Validamos que se pueda actualizar el comprobantes mientras esta en Estado de Elaborado
					BEGIN try	
						begin tran
							Update tOrdenDeCompra
							set 					
								--codEmpleadoComprador=@pcodEmpleadoComprador,
								codAlmacen=@pcodAlmacen,
								SerieOrden=@pSerieOrden,  
								NroOrden=@pNroOrden,
								AplicaIGV=@pAplicaIGV,
								TasaIgv=@pTasaIgv,
								AfectaPercepcion=@pAfectaPercepcion,
								FechaEmisionOrden=@pFechaEmisionOrden,
								FechaVencimientoOrden=@pFechaVencimientoOrden,
								codTipoOrdenCompra=@pcodTipoOrdenCompra,
								CodRequerimiento=@pCodRequerimiento,
								SerieNroCotizacion=@pSerieNroCotizacion,
								idMedioPago=@pidMedioPago,
								codMoneda=@pcodMoneda,
								TipoCambio=@pTipoCambio,
								idEntExtProveedor=@pidEntExtProveedor,
								idLocacionEntExterna=@pidLocacionEntExterna,
								idContactoExtProveedor=@pidContactoExtProveedor,
								CorreoEntExtProveedor=@pCorreoEntExtProveedor,
								idLineaTelefonica=@pidLineaTelefonica,
								idEntFinanciera=@pidEntFinanciera,
								idCtaEntidadExt=@pidCtaEntidadExt,
								codEmpleadoConsignado=@pcodEmpleadoConsignado,
								idEntExtTransportista=@pidEntExtTransportista,
								idAgenciaDestino=@pidAgenciaDestino,
								TiempoEntrega=@pTiempoEntrega,
								FechaAproxLlegada=@pFechaAproxLlegada,
								IdTipoComp=@pIdTipoComp,
								NroDocumento=@pNroDocumento,
								codTipoEnvio=@pcodTipoEnvio,
								Atenciona=@pAtenciona,
								Clave=@pClave,
								Referencia=@pReferencia,
								DsctoG1=@pDsctoG1,
								DsctoG2=@pDsctoG2,
								SubTotalSinIgV=@pSubTotalSinIgV,
								TotalIGV=@pTotalIGV,
								Descuento=@pDescuento,
								Percepcion=@pPercepcion,
								OtrosGastos=@pOtrosGastos,
								Total=@pTotal,
								ObservacionesOrdCompra=@pObservacionesOrdCompra,
								ObservacionesOrdCompraInterna=@pObservacionesOrdCompraInterna,
								UserMod=@pUserMod,
								FechaMod=GETDATE()
							Where idOrdenCompra=@pidOrdenCompra
						commit
					END try
					begin catch
						ROLLBACK
						PRINT ERROR_MESSAGE()
						return 
					end catch
			end
		
	if exists(Select * from tEstadoOrdenCompra where idOrdenCompra=@pidOrdenCompra  and idTipoEstadoOrdenCompra=@pidTipoEstOrdCompra and EstReg='1' and EstActual='1')
		begin
			Update tEstadoOrdenCompra
			set EstActual='1',FechaMod=getdate() --, UserMod=@pUserMod, 
			where idOrdenCompra=@pidOrdenCompra and EstReg='1' and EstActual='1'
		end
	else
		begin
			-- Desactivamos el anterior estado
			Update tEstadoOrdenCompra
			set EstActual='0'
			where idOrdenCompra=@pidOrdenCompra and EstReg='1' and EstActual='1'
			--Insertamos el nuevo estado del DOcumento de Venta
			insert into tEstadoOrdenCompra
			(idOrdenCompra,idTipoEstadoOrdenCompra,UserMod,FechaCambioEstado,EstActual,EstReg,FechaMod)
			values
			(@pidOrdenCompra,@pidTipoEstOrdCompra,@pUserMod,getdate(),'1','1',getdate())
				
		end
end

