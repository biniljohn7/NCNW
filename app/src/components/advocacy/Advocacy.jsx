import React, { useState, useEffect } from 'react'

import { TabContent, TabPane, Nav, NavItem, NavLink } from 'reactstrap'
import Wrapper from './advocacy.style.js'
import { listAdvocacy } from '../../api/advocacyApi'
import { ToastsStore } from 'react-toasts'
import Pagination from '../../UI/pagination/pagination'
import classnames from 'classnames'
import { Spinner } from 'reactstrap'
import Select from 'react-select'
import Input from '../../UI/input/input'
import { ADVOCACY_LOCATION } from '../../helper/constant'
import ClampLines from 'react-clamp-lines'

const COL_NO = window.innerWidth < 768 ? 2 : window.innerWidth <= 1024 ? 3 : 4
const COL_WIDTH =
	window.innerWidth < 768 ? '50%' : window.innerWidth <= 1024 ? '33%' : '25%'

const LIMIT = 10

const IMAGE_SIZE =
	window.innerWidth < 768
		? '100px'
		: window.innerWidth <= 1440
			? '150px'
			: '200px'

const Advocacy = (props) => {
	const [advocacy, setAdvocacy] = useState([])
	const [isLoading, setLoading] = useState(true)
	const [currentPage, setPage] = useState(1)
	const [totalPage, setTotalPage] = useState(0)
	const [activeTab, setActiveTab] = useState(
		props.location.state && props.location.state.advocacyType
			? props.location.state.advocacyType
			: 'Issues',
	)
	const [search, setSearch] = useState('')
	const [type, setType] = useState(ADVOCACY_LOCATION[0])

	useEffect(() => {
		getList({
			advocacyType: activeTab,
			pageId: currentPage,
			search: search,
			type: type.value,
		})
	}, [])

	useEffect(() => {
		setPage(1)
		setTotalPage(0)
		setSearch('')
		setType(ADVOCACY_LOCATION[0])
		getList({
			advocacyType: activeTab,
			type: type.value,
			pageId: 1,
			search: '',
		})
	}, [activeTab])

	document.title = 'Advocacy - ' + window.seoTagLine;

	const getList = (body) => {
		setLoading(true)
		listAdvocacy(body)
			.then((res) => {
				if (res.success) {
					setAdvocacy(res.data.list)
					setPage(res.data.currentPageNo)
					setTotalPage(res.data.totalPages)
				}
				setLoading(false)
			})
			.catch((err) => {
				console.error(err)
				setLoading(false)
				ToastsStore.error('Something went wrong!')
			})
	}

	const toggle = (tab) => {
		if (activeTab !== tab) setActiveTab(tab)
	}

	const ListAdvocacy = () => {
		return isLoading ? (
			<div className="text-center custom-spinner mtp-20">
				<Spinner color="danger" />
			</div>
		) : advocacy.length > 0 ? (
			<React.Fragment>
				<div className="advo-section inner-advo">
					<div className="advo-box">
						<div className="ad-left">
							{
								advocacy.map(
									(ev) => {
										function click() {
											props.history.push(
												`${/\/(advocacy)$/i.test(props.history.location.pathname) ? '/advocacy/' : '/programs/'}${activeTab}/${ev.title.replaceAll('/', ' ')}`,
												{
													advocacyId: ev.advocacyId,
													advocacyType: activeTab,
												},
											)
										}
										function openPdf() {
											if (ev.pdfUpload) {
												window.open(ev.pdfUpload, "_blank", "noopener,noreferrer");
											}
										};
										return <div
											className="adv-item"
											key={ev.advocacyId}
										>
											<div className="image-box">
												{ev.image ? (<img
													src={ev.image}
													alt={ev.title.substr(0, 10) + '...'}
													onClick={openPdf}
												/>) : (
													<div className='empty-img' onClick={openPdf}>
														<span className="material-symbols-outlined empty-icn">
															hide_image
														</span>
													</div>
												)}
											</div>
											<h2>
												{ev.title}
											</h2>
											{/* <div className="mt-10 text-secondary">
                        {ev.recipientName}
                      </div> */}
											{/* <div className="button-box">
                        <span onClick={click}>READ MORE</span>
                      </div> */}
										</div>;
									}
								)
							}
						</div>
					</div>
				</div>
				<div className="ncnw-link button-box">
					<a href="https://ncnw.org/advocacy-policy/" target='__blank'>
						FIND MORE
					</a>
				</div>

				<div className="pagination">
					<Pagination
						activePage={currentPage}
						length={totalPage * LIMIT}
						count={LIMIT}
						handler={(pageNumber) => {
							setPage(pageNumber)
							getList({
								advocacyType: activeTab,
								pageId: pageNumber,
								search: search,
								type: type.value,
							})
						}}
					/>
				</div>
			</React.Fragment>
		) : (
			<div className="border ptb-50 plr-20 text-center text-bold mt-20">
				No Record Found!
			</div>
		)
	}

	return (
		<Wrapper col={COL_NO} width={COL_WIDTH} size={IMAGE_SIZE}>

			<div className="advo-section inner-advo">
				<div className="head-box">
					<div className="container">
						<h2>
							{
								/\/(advocacy)$/i.test(props.history.location.pathname) ? 'advocacy' : 'programs'
							}
						</h2>
						<div className="head-tab">
							{/* <ul>
                {
                  (ADVOCACY_LOCATION || []).map(
                    function (e) {
                      return <li>
                        <span
                          className={
                            "nav-link" + (
                              type.value === e.value ?
                                ' active' : ''
                            )
                          }
                          onClick={
                            function () {
                              setType(e);
                              getList({
                                advocacyType: activeTab,
                                pageId: currentPage,
                                search: search,
                                type: e.value,
                              });
                            }
                          }
                        >
                          {e.label}
                        </span>
                      </li>
                    }
                  )
                }
              </ul> */}
						</div>
					</div>
				</div>
			</div>

			<div className="container">
				<Nav tabs>
					<NavItem>
						<NavLink
							className={classnames({ active: activeTab === 'Issues' })}
							onClick={() => {
								toggle('Issues')
							}}
						>
							Issues
						</NavLink>
					</NavItem>
					{/* <NavItem>
            <NavLink
              className={classnames({ active: activeTab === 'Submitted' })}
              onClick={() => {
                toggle('Submitted')
              }}
            >
              Submitted
            </NavLink>
          </NavItem> */}
				</Nav>

				<TabContent activeTab={activeTab}>
					<TabPane tabId="Issues" className="mt-20">
						<div className="row">
							<div className="col-7 col-sm-7 col-md-5 col-lg-4 col-xl-4 position-relative">
								<Input
									type="text"
									placeholder="Search from here..."
									id="advocacy_search_issues"
									contentFontSize={'fs-14'}
									className="search"
									value={search}
									onChange={(e) => setSearch(e.target.value)}
									onEnter={() => {
										setPage(1)
										setTotalPage(0)
										getList({
											advocacyType: activeTab,
											pageId: 1,
											search: search,
											type: type.value,
										})
									}}
								/>
								<i
									className="fa fa-search eye pwd cursor-pointer"
									onClick={(e) => {
										setPage(1)
										setTotalPage(0)
										getList({
											advocacyType: activeTab,
											pageId: 1,
											search: search,
											type: type.value,
										})
									}}
								></i>
							</div>
						</div>
						<ListAdvocacy />
					</TabPane>
					<TabPane tabId="Submitted" className="mt-20">
						<div className="row">
							<div className="col-12 col-sm-12 col-md-4 col-lg-5 col-xl-6"></div>
							<div className="col-5 col-sm-5 col-md-3 col-lg-3 col-xl-2 m-auto cursor-pointer">
								{/* <img
                      src={Filter}
                      alt="filter"
                      className="m-auto cursor-pointer"
                    /> */}
								<Select
									options={ADVOCACY_LOCATION || []}
									onChange={(op) => {
										setType(op)
										getList({
											advocacyType: activeTab,
											pageId: currentPage,
											search: search,
											type: op.value,
										})
									}}
									value={type}
									placeholder="Choose Location"
								// styles={{
								//   control: (base, state) => ({
								//     ...base,
								//     borderRadius: '17px',
								//   }),
								//   placeholder: (base, state) => ({
								//     ...base,
								//     marginLeft: '7px',
								//   }),
								// }}
								/>
							</div>
							<div className="col-7 col-sm-7 col-md-5 col-lg-4 col-xl-4 position-relative">
								<Input
									type="text"
									placeholder="Search from here..."
									id="advocacy_search_submitted"
									contentFontSize={'fs-14'}
									className="search"
									value={search}
									onChange={(e) => setSearch(e.target.value)}
									onEnter={() => {
										setPage(1)
										setTotalPage(0)
										getList({
											advocacyType: activeTab,
											pageId: 1,
											search: search,
											type: type.value,
										})
									}}
								/>
								<i
									className="fa fa-search eye pwd cursor-pointer"
									onClick={(e) => {
										setPage(1)
										setTotalPage(0)
										getList({
											advocacyType: activeTab,
											pageId: 1,
											search: search,
											type: type.value,
										})
									}}
								></i>
							</div>
						</div>
						<ListAdvocacy />
					</TabPane>
				</TabContent>
			</div>
			<div className="head-box mb40">
				<div className="container">
					<h2>Request / Reporting Tools:</h2>
				</div>
			</div>
			<div className="container">
				<div className="three-cl-row">
					<div className="col-3">
						<a href="https://ncnw.jotform.com/250765581912058" target="_blank" className='col-link'>
							<span className="material-symbols-outlined icn">
								gavel
							</span>
							<span>NCNW Advocacy<br></br > Action Form</span>
						</a>
					</div>
					<div className="col-3">
						<a href="https://ncnw.jotform.com/251534095760054" target="_blank" className='col-link'>
							<span className="material-symbols-outlined icn">
								balance
							</span>
							<span>Social Justice<br></br > Advocacy Action Report</span>
						</a>
					</div>
				</div>
			</div>
		</Wrapper>
	)
}

export default Advocacy
