import React, {RefAttributes} from 'react';
import MUIDataTable, {MUIDataTableColumn, MUIDataTableOptions, MUIDataTableProps} from 'mui-datatables';
import {MuiThemeProvider, Theme, useTheme} from '@material-ui/core';
import {cloneDeep, merge, omit} from 'lodash';
import DebouncedTableSearch from "./DebouncedTableSearch"

// import DebouncedTableSearch from 'DebouncedTableSearch';

export interface TableColumn extends MUIDataTableColumn {
	width?: string;
	isLoading?: boolean;
}

export interface MuiDataTableRefComponent {
	changePage: (page: number) => void;
	changeRowsPerPage: (rowsPerPage: number) => void;
}

const makeDefaultOptions = (debouncedSearchTime?:any): MUIDataTableOptions => ({
	print: false,
	download: false,
	textLabels: {
		body: {
			noMatch: "Nenhum registro encontrado",
			toolTip: "Classificar",
		},
		pagination: {
			next: "Próxima página",
			previous: "Página anterior",
			rowsPerPage: "Por página",
			displayRows: "de",
		},
		toolbar: {
			search: "Busca",
			downloadCsv: "Download CSV",
			print: "Imprimir",
			viewColumns: "Ver colunas",
			filterTable: "Filtrar tabela",
		},
		filter: {
			all: "Todos",
			title: "FILTROS",
			reset: "LIMPAR",
		},
		viewColumns: {
			title: "Ver colunas",
			titleAria: "Ver/Esconder Colunas da Tabela",
		},
		selectedRows: {
			text: "registro(s) selecionados",
			delete: "Excluir",
			deleteAria: "Excluir registros selecionados"
		},
	},
	customSearchRender: (searchText: string, handleSearch: any, hideSearch: any, options: any) => {
		return <DebouncedTableSearch
			searchText={searchText}
			onSearch={handleSearch}
			onHide={hideSearch}
			options={options}
			debounceTime={debouncedSearchTime}
		/>
	}
});

interface TableProps extends MUIDataTableProps, RefAttributes<MuiDataTableRefComponent> {
	columns: TableColumn[];
	loading?: boolean;
	debouncedSearchTime?: number;
}

const Table = React.forwardRef<MuiDataTableRefComponent, TableProps>((props, ref) => {

	function extractMuiDatableColumns(columns: TableColumn[]): MUIDataTableColumn[] {
		setColumnsWidth(columns);
		return columns.map(column => omit(column, 'width'));
	}

	function setColumnsWidth(columns: TableColumn[]) {
		columns.forEach((column, key: number) => {
			if (column.width) {
				const overrides = theme.overrides as any;
				overrides.MUIDataTableHeadCell.fixedHeader[`&:nth-child(${key + 2})`] = {
					width: column.width
				}
			}
		});
	}

	function applyLoading() {
		const textLabels = (newProps.options as any).textLabels;
		textLabels.body.noMatch =
			newProps.loading === true
				? 'Carregando...'
				: textLabels.body.noMatch
	}

	function getOriginalMuiDataTableProps() {
		return {
			...omit(newProps, 'isLoading'),
			ref
		};
	}

	const theme = cloneDeep<Theme>(useTheme());

	const defaultOptions = makeDefaultOptions(props.debouncedSearchTime);

	const newProps = merge(
		{options: cloneDeep(defaultOptions)},
		props,
		{columns: extractMuiDatableColumns(props.columns)}
		);

	applyLoading();
	/*
		Não tem o applyResponsive()
		Pois na versão que estou usando do Table o parâmetro {responsive: 'standard'} tem o mesmo efeito,
		e as opções da aula não existem mais
	 */

	const originalProps = getOriginalMuiDataTableProps();

	return (
		<MuiThemeProvider theme={theme}> <MUIDataTable {...originalProps}/> </MuiThemeProvider>
	);
});

export default Table;


export function makeActionsStyle(column: any) {

	return (theme: any) => {
		const copyTheme = cloneDeep(theme);
		const selector = `&[data-testid^="MuiDataTableBodyCell-${column}"]`;

		(copyTheme.overrides as any).MUIDataTableBodyCell.root[selector] = {
			paddingTop: '0px',
			paddingBottom: '0px',
		}
		return copyTheme
	}

}