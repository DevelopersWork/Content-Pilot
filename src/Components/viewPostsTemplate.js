import React from 'react';

import { Table, Pagination } from 'react-bootstrap';

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
	// props.fetchPosts(props.options);
	return (
		<React.Fragment>
			<Table bordered striped hover className="min-vh-80">
				<thead>
					<tr>{populateHeader(props.columns || [])}</tr>
				</thead>
				<tbody>{populateRows(props.posts || [], props.columns || [])}</tbody>
			</Table>
			{setupPagination(
				(props.total_posts || 0) / props.posts_per_page,
				props.current_page
			)}
		</React.Fragment>
	);
};

export default viewPostsTemplate;
