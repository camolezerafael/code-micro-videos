import { createActions, createReducer } from 'reduxsauce';
import * as Typeings from './types';

export const { Types, Creators } = createActions<{
	SET_SEARCH: string,
	SET_PAGE: string,
	SET_PER_PAGE: string,
	SET_ORDER: string,
	SET_RESET: string,
}, {
	setSearch( payload: Typeings.SetSearchAction['payload'] ): Typeings.SetSearchAction
	setPage( payload: Typeings.SetPageAction['payload'] ): Typeings.SetPageAction
	setPerPage( payload: Typeings.SetPerPageAction['payload'] ): Typeings.SetPerPageAction
	setOrder( payload: Typeings.SetOrderAction['payload'] ): Typeings.SetOrderAction
	setReset(): any
}>
( {
	  setSearch: [ 'payload' ],
	  setPage: [ 'payload' ],
	  setPerPage: [ 'payload' ],
	  setOrder: [ 'payload' ],
	  setReset: []
  } );


export const INITIAL_STATE: Typeings.FilterState = {
	search: null,
	pagination: {
		page: 1,
		per_page: 10
	},
	order: {
		sort: null,
		dir: null
	}
};

const reducer = createReducer<Typeings.FilterState, Typeings.Actions>( INITIAL_STATE, {
	[ Types.SET_SEARCH ]: setSearch,
	[ Types.SET_PAGE ]: setPage,
	[ Types.SET_PER_PAGE ]: setPerPage,
	[ Types.SET_ORDER ]: setOrder,
	[ Types.SET_RESET ]: setReset
} );

export default reducer;

function setSearch( state = INITIAL_STATE, action: Typeings.SetSearchAction ): Typeings.FilterState {
	return {
		...state,
		search: action.payload.search,
		pagination: {
			...state.pagination,
			page: 1
		}
	};
}

function setPage( state = INITIAL_STATE, action: Typeings.SetPageAction ): Typeings.FilterState {
	return {
		...state,
		pagination: {
			...state.pagination,
			page: action.payload.page
		}
	};
}

function setPerPage( state = INITIAL_STATE, action: Typeings.SetPerPageAction ): Typeings.FilterState {
	return {
		...state,
		pagination: {
			...state.pagination,
			per_page: action.payload.per_page
		}
	};
}

function setOrder( state = INITIAL_STATE, action: Typeings.SetOrderAction ): Typeings.FilterState {
	return {
		...state,
		pagination: {
			...state.pagination,
			page: 1
		},
		order: {
			sort: action.payload.sort,
			dir: action.payload.dir
		}
	};
}

function setReset( state = INITIAL_STATE, action: any ): Typeings.FilterState {
	return {
		...INITIAL_STATE,
		search: {
			value: null,
			update: true
		},
		order: {
			sort: null,
			dir: null
		}
	};
}