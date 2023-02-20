import React, { lazy } from 'react';

import { Table, Pagination } from 'react-bootstrap';

const Placeholder = lazy(() => import('./placeholder'));

function populateColumn(name, value) {
	return <td key={name}>{value}</td>;
}

function populateRows(rows, columns) {
	return rows.map((row) => (
		<tr key={JSON.stringify(row)}>
			{columns.map((column) =>
				populateColumn(column.name, row[column.name] || '')
			)}
		</tr>
	));
}

function populateHeader(columns) {
	return columns.map((column) => <th key={column.name}>{column.value}</th>);
}

function getPagination(page, current_page) {
	return (
		<Pagination.Item key={page} active={current_page === page - 1}>
			{page}
		</Pagination.Item>
	);
}

// eslint-disable-next-line no-unused-vars
function setupPagination(total_pages, current_page) {
	return (
		<Pagination>
			<Pagination.First disabled={current_page === 0} />
			<Pagination.Prev disabled={current_page === 0} />

			{/* <Pagination.Ellipsis /> */}
			{[1, 2, 3, 4, 5].map((page) => getPagination(page, current_page))}

			<Pagination.Next disabled={current_page === total_pages - 1} />
			<Pagination.Last disabled={current_page === total_pages - 1} />
		</Pagination>
	);
}

const viewPostsTemplate = (props) => {
	const posts = props.posts || [];
	const columns = props.columns || [];
	if (!columns.length || !posts.length) return <Placeholder />;
	return (
		<React.Fragment>
			<Table bordered striped hover className="min-vh-80">
				<thead>
					<tr>{populateHeader(columns)}</tr>
				</thead>
				<tbody>{populateRows(posts, columns)}</tbody>
			</Table>
			{/* TODO: Pagenation functionality */}
			{/* {setupPagination(
				(props.total_posts || 0) / props.posts_per_page,
				props.current_page
			)} */}
		</React.Fragment>
	);
};

export default viewPostsTemplate;
