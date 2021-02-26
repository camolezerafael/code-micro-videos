import { Dispatch, Reducer, useReducer, useState } from 'react';
import reducer, { Creators, INITIAL_STATE } from '../store/filter';
import { Actions as FilterActions, FilterState } from '../store/filter/types';
import { MUIDataTableColumn } from 'mui-datatables';
import { useDebounce } from 'use-debounce';
import { useHistory } from 'react-router-dom';
import { History } from 'history';
import { isEqual } from 'lodash';

interface FilterManagerOptions {
	columns: MUIDataTableColumn[];
	rowsPerPage: number;
	rowsPerPageOptions: number[];
	debounceTime: number;
	history: History;
}

interface UseFilterOptions extends Omit<FilterManagerOptions, 'history'> {
	columns: MUIDataTableColumn[];
	rowsPerPage: number;
	rowsPerPageOptions: number[];
	debounceTime: number;
}

export default function useFilter( options: UseFilterOptions ) {
	const history = useHistory();
	const filterManager = new FilterManager( { ...options, history } );
	const [ filterState, dispatch ] = useReducer<Reducer<FilterState, FilterActions>>( reducer, INITIAL_STATE );
	const [ debouncedFilterState ] = useDebounce( filterState, options.debounceTime );
	const [ totalRecords, setTotalRecords ] = useState<number>( 0 );

	filterManager.state = filterState;
	filterManager.dispatch = dispatch;

	filterManager.applyOrderInColumns();

	return {
		columns: filterManager.columns,
		filterManager,
		filterState,
		debouncedFilterState,
		dispatch,
		totalRecords,
		setTotalRecords
	};
}

export class FilterManager {
	state: FilterState = null as any;
	dispatch: Dispatch<FilterActions> = null as any;
	columns: MUIDataTableColumn[];
	rowsPerPage: number;
	rowsPerPageOptions: number[];
	history: History;

	constructor( options: FilterManagerOptions ) {
		const { columns, rowsPerPage, rowsPerPageOptions, history } = options;

		this.columns = columns;
		this.rowsPerPage = rowsPerPage;
		this.rowsPerPageOptions = rowsPerPageOptions;
		this.history = history;
	}

	changeSearch( value: any ) {
		this.dispatch( Creators.setSearch( { search: value } ) );
	}

	changePage( page: any ) {
		this.dispatch( Creators.setPage( { page: page + 1 } ) );
	}

	changeRowsPerPage( per_page: any ) {
		this.dispatch( Creators.setPerPage( { per_page: per_page } ) );
	}

	changeColumnSort( changedColumn: any, direction: any ) {
		this.dispatch( Creators.setOrder(
			{
				sort: changedColumn,
				dir: direction.includes( 'desc' ) ? 'desc' : 'asc'
			} )
		);
	}

	applyOrderInColumns() {
		this.columns = this.columns.map( column => {
			return ( column.name === this.state.order.sort ) ? {
				...column, options: {
					...column.options,
					sortDirection: this.state.order.dir as any
				}
			} : column;
		} );
	}

	cleanSearchText( text: any ) {
		let newText = text;
		if ( text && text.value !== undefined ) {
			newText = text.value;
		}
		return newText;
	}

	pushHistory() {
		const newLocation = {
			pathName: this.history.location.pathname,
			search: '?' + new URLSearchParams( this.formatSearchParams() as any ),
			state: {
				...this.state,
				search: this.cleanSearchText( this.state.search )
			}
		};

		const oldState = this.history.location.state;
		const nextState = this.state;

		if ( isEqual( oldState, nextState ) ) {
			return;
		}
		this.history.push( newLocation );
	}

	private formatSearchParams() {
		const search = this.cleanSearchText( this.state.search );
		return {
			...( search && search !== '' && { search: search } ),
			...( this.state.pagination.page !== 1 && { page: this.state.pagination.page } ),
			...( this.state.pagination.per_page !== 15 && { per_page: this.state.pagination.per_page } ),
			...( this.state.order.sort && {
				sort: this.state.order.sort,
				dir: this.state.order.dir
			} )
		};
	}

}

const schema = yup.object().shape(
	{
		search: yup.string()
				   .transform( value => !value ? undefined : value )
				   .default( '' ),
		pagination: yup.object().shape(
			{
				page: yup.number()
						 .transform( value => isNaN( value ) || parseInt( value ) < 1 ? undefined : value )
						 .default( 1 ),
				per_page: yup.number()
							 .oneOf( [ 10, 25, 50 ] )
							 .transform( value => isNaN( value ) || parseInt( value ) < 1 ? undefined : value )
							 .default( 15 )
			} )
		order: yup.object().shape(
			{
				sort: yup.string()
						 .nullable()
						 .transform( value => {
							 const columnsName = columns
								 .filter( column => !column.options || column.options.sort !== false )
								 .map( column => column.name );
							 return columnsName.includes( value ) ? value : undefined;
						 } )
						 .default( null ),
				dir: yup.string()
						.nullable()
						.transform( value => !value || ['asc', 'desc'].includes(value.toLowerCase()) ? undefined : value)
						.default( null )
			} )
	} );