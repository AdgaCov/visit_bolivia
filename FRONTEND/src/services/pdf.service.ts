import jsPDF from 'jspdf'
import html2canvas from 'html2canvas'

interface PDFOptions {
  title: string
  subtitle?: string
  source?: string
  period?: string
}

class PDFService {
  /**
   * Agrega un encabezado profesional al PDF
   */
  private addHeader(doc: jsPDF, pageWidth: number, title: string, subtitle?: string, source?: string, period?: string): number {
    const margin = 15
    let yPosition = margin

    // Línea decorativa superior
    doc.setDrawColor(37, 99, 235) // Azul
    doc.setLineWidth(0.5)
    doc.line(margin, yPosition, pageWidth - margin, yPosition)
    yPosition += 3

    // Título principal
    doc.setFontSize(22)
    doc.setFont('helvetica', 'bold')
    doc.setTextColor(15, 23, 42) // Casi negro
    doc.text(title, margin, yPosition)
    yPosition += 8

    // Subtítulo
    if (subtitle) {
      doc.setFontSize(12)
      doc.setFont('helvetica', 'normal')
      doc.setTextColor(71, 85, 105) // Gris medio
      doc.text(subtitle, margin, yPosition)
      yPosition += 6
    }

    // Información adicional
    doc.setFontSize(9)
    doc.setTextColor(100, 116, 139) // Gris claro
    
    if (source) {
      doc.text(`Fuente: ${source}`, margin, yPosition)
      yPosition += 4
    }

    if (period) {
      doc.text(`Período: ${period}`, margin, yPosition)
      yPosition += 4
    }

    yPosition += 5

    // Línea decorativa inferior
    doc.setDrawColor(226, 232, 240) // Gris muy claro
    doc.setLineWidth(0.3)
    doc.line(margin, yPosition, pageWidth - margin, yPosition)
    yPosition += 8

    doc.setTextColor(0, 0, 0)
    return yPosition
  }

  /**
   * Agrega un gráfico con borde y título
   */
  private async addChart(
    doc: jsPDF,
    chartElement: HTMLElement | null,
    pageWidth: number,
    pageHeight: number,
    yPosition: number,
    margin: number,
    title?: string
  ): Promise<number> {
    if (!chartElement) return yPosition

    try {
      // Capturar gráfico con mayor calidad
      const canvas = await html2canvas(chartElement, {
        backgroundColor: '#ffffff',
        scale: 3, // Mayor calidad
        logging: false,
        useCORS: true,
        allowTaint: true
      })

      const imgData = canvas.toDataURL('image/png', 1.0)
      const chartWidth = pageWidth - 2 * margin - 4 // Margen para borde
      const chartHeight = (canvas.height * chartWidth) / canvas.width

      // Verificar si necesita nueva página
      if (yPosition + chartHeight + 20 > pageHeight - 20) {
        doc.addPage()
        yPosition = margin + 10
      }

      // Borde alrededor del gráfico
      doc.setDrawColor(226, 232, 240)
      doc.setLineWidth(0.5)
      doc.roundedRect(margin, yPosition, chartWidth + 4, chartHeight + 4, 2, 2, 'S')
      
      // Agregar imagen
      doc.addImage(imgData, 'PNG', margin + 2, yPosition + 2, chartWidth, chartHeight)
      
      yPosition += chartHeight + 12

      return yPosition
    } catch (error) {
      console.error('Error capturando gráfico:', error)
      return yPosition
    }
  }

  /**
   * Agrega una tabla profesional con bordes y colores alternados
   */
  private addTable(
    doc: jsPDF,
    headers: string[],
    colWidths: number[],
    data: any[][],
    startY: number,
    pageWidth: number,
    pageHeight: number,
    margin: number
  ): void {
    let yPosition = startY
    const rowHeight = 7
    const headerHeight = 8

    // Fondo del encabezado
    doc.setFillColor(37, 99, 235) // Azul
    doc.roundedRect(margin, yPosition - headerHeight + 2, pageWidth - 2 * margin, headerHeight, 2, 2, 'F')

    // Texto del encabezado
    doc.setFontSize(10)
    doc.setFont('helvetica', 'bold')
    doc.setTextColor(255, 255, 255) // Blanco
    let xPosition = margin + 3

    headers.forEach((header, index) => {
      doc.text(header, xPosition, yPosition)
      xPosition += colWidths[index]
    })

    yPosition += 3

    // Datos de la tabla
    doc.setFontSize(9)
    doc.setFont('helvetica', 'normal')
    doc.setTextColor(0, 0, 0)

    data.forEach((row, rowIndex) => {
      // Verificar si necesita nueva página
      if (yPosition > pageHeight - 25) {
        doc.addPage()
        yPosition = margin + 10

        // Reimprimir encabezado
        doc.setFillColor(37, 99, 235)
        doc.roundedRect(margin, yPosition - headerHeight + 2, pageWidth - 2 * margin, headerHeight, 2, 2, 'F')
        doc.setFont('helvetica', 'bold')
        doc.setTextColor(255, 255, 255)
        xPosition = margin + 3
        headers.forEach((header, index) => {
          doc.text(header, xPosition, yPosition)
          xPosition += colWidths[index]
        })
        yPosition += 3
        doc.setFont('helvetica', 'normal')
        doc.setTextColor(0, 0, 0)
      }

      // Fondo alternado para filas
      if (rowIndex % 2 === 0) {
        doc.setFillColor(248, 250, 252) // Gris muy claro
        doc.roundedRect(margin, yPosition - rowHeight + 2, pageWidth - 2 * margin, rowHeight, 1, 1, 'F')
      }

      // Bordes de celda
      doc.setDrawColor(226, 232, 240)
      doc.setLineWidth(0.2)
      xPosition = margin
      headers.forEach((_, index) => {
        if (index > 0) {
          doc.line(xPosition, yPosition - rowHeight + 2, xPosition, yPosition + 2)
        }
        xPosition += colWidths[index]
      })

      // Texto de las celdas
      xPosition = margin + 3
      row.forEach((cell, cellIndex) => {
        doc.text(cell, xPosition, yPosition)
        xPosition += colWidths[cellIndex]
      })

      yPosition += rowHeight
    })

    // Borde exterior de la tabla
    doc.setDrawColor(203, 213, 225)
    doc.setLineWidth(0.5)
    doc.roundedRect(margin, startY - headerHeight + 2, pageWidth - 2 * margin, yPosition - startY + headerHeight - 2, 2, 2, 'S')
  }

  /**
   * Agrega pie de página profesional
   */
  private addFooter(doc: jsPDF, pageWidth: number, pageHeight: number, margin: number, pageNum: number, totalPages: number): void {
    const footerY = pageHeight - 12

    // Línea superior del footer
    doc.setDrawColor(226, 232, 240)
    doc.setLineWidth(0.3)
    doc.line(margin, footerY - 3, pageWidth - margin, footerY - 3)

    doc.setFontSize(8)
    doc.setFont('helvetica', 'normal')
    doc.setTextColor(100, 116, 139)

    // Fecha
    const date = new Date().toLocaleDateString('es-BO', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    })
    doc.text(`Generado el ${date}`, margin, footerY + 2)

    // Número de página
    doc.text(`Página ${pageNum} de ${totalPages}`, pageWidth - margin - 20, footerY + 2)
  }

  /**
   * Genera un PDF con gráficos y tabla de datos de viajeros
   */
  async generateTravelersPDF(
    chart1Element: HTMLElement | null,
    chart2Element: HTMLElement | null,
    tableData: Array<{
      year: number
      air: number
      road: number
      total: number
      yoy: number | null
      foreignPercent: number
      provisional?: boolean
    }>,
    options: PDFOptions
  ): Promise<void> {
    const doc = new jsPDF('landscape', 'mm', 'a4')
    const pageWidth = doc.internal.pageSize.getWidth()
    const pageHeight = doc.internal.pageSize.getHeight()
    const margin = 15

    // Página 1: Encabezado y gráficos
    let yPosition = this.addHeader(
      doc,
      pageWidth,
      options.title,
      options.subtitle,
      options.source,
      options.period
    )

    // Agregar gráficos
    yPosition = await this.addChart(doc, chart1Element, pageWidth, pageHeight, yPosition, margin)
    yPosition = await this.addChart(doc, chart2Element, pageWidth, pageHeight, yPosition, margin)

    // Página 2: Tabla
    doc.addPage()
    yPosition = margin

    // Título de la tabla
    doc.setFontSize(18)
    doc.setFont('helvetica', 'bold')
    doc.setTextColor(15, 23, 42)
    doc.text('Serie Histórica de Viajeros Internacionales', margin, yPosition)
    yPosition += 12

    // Preparar datos de la tabla
    const headers = ['Año', 'Aéreo', 'Carretero', 'Total', 'YoY', '% Extranjeros']
    const colWidths = [20, 30, 30, 30, 25, 30]
    const tableRows: string[][] = tableData.map((row) => [
      row.provisional ? `${row.year} (p)` : row.year.toString(),
      this.formatNumber(row.air),
      this.formatNumber(row.road),
      this.formatNumber(row.total),
      row.yoy !== null ? this.formatPercent(row.yoy) : '—',
      this.formatPercent(row.foreignPercent)
    ])

    this.addTable(doc, headers, colWidths, tableRows, yPosition, pageWidth, pageHeight, margin)

    // Agregar footer a todas las páginas
    const totalPages = doc.getNumberOfPages()
    for (let i = 1; i <= totalPages; i++) {
      doc.setPage(i)
      this.addFooter(doc, pageWidth, pageHeight, margin, i, totalPages)
    }

    // Descargar PDF
    doc.save(`estadisticas-viajeros-${new Date().getTime()}.pdf`)
  }

  /**
   * Genera un PDF con gráficos y tabla de datos de gastos
   */
  async generateExpensesPDF(
    chart1Element: HTMLElement | null,
    chart2Element: HTMLElement | null,
    tableData: Array<{
      year: number
      accommodation: number
      goods: number
      services: number
      total: number
      yoy: number | null
      provisional?: boolean
    }>,
    options: PDFOptions
  ): Promise<void> {
    const doc = new jsPDF('landscape', 'mm', 'a4')
    const pageWidth = doc.internal.pageSize.getWidth()
    const pageHeight = doc.internal.pageSize.getHeight()
    const margin = 15

    // Página 1: Encabezado y gráficos
    let yPosition = this.addHeader(
      doc,
      pageWidth,
      options.title,
      options.subtitle,
      options.source,
      options.period
    )

    // Agregar gráficos
    yPosition = await this.addChart(doc, chart1Element, pageWidth, pageHeight, yPosition, margin)
    yPosition = await this.addChart(doc, chart2Element, pageWidth, pageHeight, yPosition, margin)

    // Página 2: Tabla
    doc.addPage()
    yPosition = margin

    // Título de la tabla
    doc.setFontSize(18)
    doc.setFont('helvetica', 'bold')
    doc.setTextColor(15, 23, 42)
    doc.text('Serie Histórica de Gasto Turístico', margin, yPosition)
    yPosition += 12

    // Preparar datos de la tabla
    const headers = ['Año', 'Alojamiento', 'Bienes', 'Servicios', 'Total', 'YoY']
    const colWidths = [20, 32, 32, 32, 32, 28]
    const tableRows: string[][] = tableData.map((row) => [
      row.provisional ? `${row.year} (p)` : row.year.toString(),
      `$${row.accommodation.toFixed(2)}M`,
      `$${row.goods.toFixed(2)}M`,
      `$${row.services.toFixed(2)}M`,
      `$${row.total.toFixed(2)}M`,
      row.yoy !== null ? this.formatPercent(row.yoy) : '—'
    ])

    this.addTable(doc, headers, colWidths, tableRows, yPosition, pageWidth, pageHeight, margin)

    // Agregar footer a todas las páginas
    const totalPages = doc.getNumberOfPages()
    for (let i = 1; i <= totalPages; i++) {
      doc.setPage(i)
      this.addFooter(doc, pageWidth, pageHeight, margin, i, totalPages)
    }

    // Descargar PDF
    doc.save(`estadisticas-gastos-${new Date().getTime()}.pdf`)
  }

  private formatNumber(value: number): string {
    return value.toLocaleString('es-BO')
  }

  private formatPercent(value: number): string {
    if (Number.isNaN(value)) return '—'
    const formatted = value.toFixed(1)
    return `${value >= 0 ? '+' : ''}${formatted}%`
  }
}

export default new PDFService()
