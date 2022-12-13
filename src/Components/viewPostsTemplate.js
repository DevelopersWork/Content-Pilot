import React from 'react';

import { Row, Col, Container } from 'react-bootstrap';
import { Table, Pagination } from 'react-bootstrap';
import { Dropdown, DropdownButton, Button } from 'react-bootstrap';

const viewPostsTemplate = (props) => {
	return (
		<React.Fragment>
			<Table bordered striped hover className="min-vh-80">
				<thead>
					<tr>
						<th>#</th>
						<th>Title</th>
						<th>Categories</th>
						<th>Tags</th>
						<th>Author</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>1</td>
						<td>Key for YouTube</td>
						<td></td>
						<td></td>
						<td>DevelopersWork</td>
						<td>1999-01-01 00:00:00.000</td>
						<td>
							<DropdownButton
								id="dropdown-basic-button"
								title="Dropdown button"
							>
								<Dropdown.Item href="#/action-1">Action</Dropdown.Item>
								<Dropdown.Item href="#/action-2">Another action</Dropdown.Item>
								<Dropdown.Item href="#/action-3">Something else</Dropdown.Item>
							</DropdownButton>
						</td>
					</tr>
					<tr>
						<td>1</td>
						<td>Key for YouTube</td>
						<td></td>
						<td></td>
						<td>DevelopersWork</td>
						<td>1999-01-01 00:00:00.000</td>
						<td>
							<DropdownButton
								id="dropdown-basic-button"
								title="Dropdown button"
							>
								<Dropdown.Item href="#/action-1">Action</Dropdown.Item>
								<Dropdown.Item href="#/action-2">Another action</Dropdown.Item>
								<Dropdown.Item href="#/action-3">Something else</Dropdown.Item>
							</DropdownButton>
						</td>
					</tr>
					<tr>
						<td>1</td>
						<td>Key for YouTube</td>
						<td></td>
						<td></td>
						<td>DevelopersWork</td>
						<td>1999-01-01 00:00:00.000</td>
						<td>
							<DropdownButton
								id="dropdown-basic-button"
								title="Dropdown button"
							>
								<Dropdown.Item href="#/action-1">Action</Dropdown.Item>
								<Dropdown.Item href="#/action-2">Another action</Dropdown.Item>
								<Dropdown.Item href="#/action-3">Something else</Dropdown.Item>
							</DropdownButton>
						</td>
					</tr>
				</tbody>
			</Table>
			<Container className="justify-content-end">
				<Pagination>
					<Pagination.First />
					<Pagination.Prev />
					<Pagination.Item>{1}</Pagination.Item>
					<Pagination.Ellipsis />

					<Pagination.Item>{10}</Pagination.Item>
					<Pagination.Item>{11}</Pagination.Item>
					<Pagination.Item active>{12}</Pagination.Item>
					<Pagination.Item>{13}</Pagination.Item>
					<Pagination.Item disabled>{14}</Pagination.Item>

					<Pagination.Ellipsis />
					<Pagination.Item>{20}</Pagination.Item>
					<Pagination.Next />
					<Pagination.Last />
				</Pagination>
			</Container>
		</React.Fragment>
	);
};

export default viewPostsTemplate;
